<?php
// app/Models/Cliente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Events\ClienteActualizadoEvent;
use App\Events\ClienteMovidoEvent;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'estado',
        'origen',
        'position',
        'total_compras',
        'compras_ultimo_mes',
        'ticket_promedio',
        'ingreso_total_generado',
        'etiqueta_riesgo',
        'dias_sin_comprar',
        'fecha_primera_visita',
        'fecha_primera_compra',
        'fecha_ultima_compra',
        'fecha_ultimo_contacto',
        'notas',
    ];

    protected $casts = [
        'fecha_primera_visita' => 'date',
        'fecha_primera_compra' => 'date',
        'fecha_ultima_compra' => 'date',
        'fecha_ultimo_contacto' => 'datetime',
        'ticket_promedio' => 'decimal:2',
        'ingreso_total_generado' => 'decimal:2',
    ];

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // EVENTOS DE BROADCASTING
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    protected static function booted(): void
    {
        // Cuando se crea un cliente
        static::created(function (Cliente $cliente) {
            broadcast(new ClienteActualizadoEvent($cliente, 'creado'));
        });

        // Cuando se actualiza un cliente
        static::updated(function (Cliente $cliente) {
            // Detectar si cambió de estado (movimiento en Kanban)
            if ($cliente->isDirty('estado')) {
                $estadoAnterior = $cliente->getOriginal('estado');
                $estadoNuevo = $cliente->estado;

                // Solo disparar si realmente cambió de columna
                if ($estadoAnterior !== $estadoNuevo) {
                    broadcast(new ClienteMovidoEvent(
                        $cliente,
                        $estadoAnterior,
                        $estadoNuevo
                    ));
                }
            }
            // Verificar si cambió la posición dentro de la misma columna
            elseif ($cliente->isDirty('position')) {
                $posicionAnterior = $cliente->getOriginal('position');
                $posicionNueva = $cliente->position;

                // Solo disparar si la posición realmente cambió de valor
                if ($posicionAnterior !== $posicionNueva) {
                    broadcast(new ClienteActualizadoEvent($cliente, 'actualizado'));
                }
            }
            // Cambios en otros atributos (nombre, teléfono, etc.)
            elseif ($cliente->wasChanged()) {
                broadcast(new ClienteActualizadoEvent($cliente, 'actualizado'));
            }
        });

        // Cuando se elimina un cliente
        static::deleted(function (Cliente $cliente) {
            broadcast(new ClienteActualizadoEvent($cliente, 'eliminado'));
        });
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // RELACIONES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // ACCESSORS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    public function getEsEnRiesgoAttribute(): bool
    {
        return $this->etiqueta_riesgo !== 'ninguno';
    }

    public function getOrigenLabelAttribute(): string
    {
        return $this->origen === 'curioso_convertido' ? '🏷️ Ex-Curioso' : '🏷️ Directo';
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // SCOPES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    public function scopeEnRiesgo($query)
    {
        return $query->where('etiqueta_riesgo', '!=', 'ninguno');
    }

    public function scopeCuriosos($query)
    {
        return $query->where('estado', 'curioso');
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['primerizo', 'recurrente', 'vip']);
    }

    public function scopeFrios($query)
    {
        return $query->where('estado', 'frio');
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // MÉTODOS DE NEGOCIO
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Actualiza todas las métricas del cliente basándose en sus compras
     */
    public function actualizarMetricas(): void
    {
        // Compras del último mes
        $comprasUltimoMes = $this->compras()
            ->where('fecha_compra', '>=', now()->subMonth())
            ->count();

        // Calcular totales
        $totalCompras = $this->compras()->count();
        $ingresoTotal = $this->compras()->sum('monto');
        $ticketPromedio = $totalCompras > 0 ? $this->compras()->avg('monto') : 0;

        // Días sin comprar
        $diasSinComprar = $this->fecha_ultima_compra
            ? now()->diffInDays($this->fecha_ultima_compra)
            : 0;

        // Actualizar en BD
        $this->update([
            'total_compras' => $totalCompras,
            'compras_ultimo_mes' => $comprasUltimoMes,
            'ingreso_total_generado' => $ingresoTotal,
            'ticket_promedio' => $ticketPromedio,
            'dias_sin_comprar' => $diasSinComprar,
        ]);

        // Auto-clasificar según compras del mes
        $this->actualizarEstado($comprasUltimoMes);

        // Detectar si está en riesgo
        $this->detectarRiesgo();
    }

    /**
     * Actualiza el estado del cliente según su frecuencia de compra
     */
    private function actualizarEstado(int $comprasUltimoMes): void
    {
        // No auto-cambiar curiosos
        if ($this->estado === 'curioso') {
            return;
        }

        $nuevoEstado = match (true) {
            $comprasUltimoMes >= 5 => 'vip',
            $comprasUltimoMes >= 2 => 'recurrente',
            $comprasUltimoMes === 1 => 'primerizo',
            default => $this->estado,
        };

        if ($nuevoEstado !== $this->estado) {
            $this->update([
                'estado' => $nuevoEstado,
                'etiqueta_riesgo' => 'ninguno', // Reset riesgo al subir de nivel
            ]);
        }
    }

    /**
     * Detecta si el cliente está en riesgo de churn
     */
    private function detectarRiesgo(): void
    {
        $riesgo = 'ninguno';

        // VIP: más de 15 días sin comprar
        if ($this->estado === 'vip' && $this->dias_sin_comprar > 15) {
            $riesgo = 'churn_risk_x1';
        }
        // Recurrente: más de 22 días sin comprar
        elseif ($this->estado === 'recurrente' && $this->dias_sin_comprar > 22) {
            $riesgo = 'churn_risk_x1';
        }
        // Cualquiera: más de 60 días = cliente frío
        elseif ($this->dias_sin_comprar > 60) {
            $this->update(['estado' => 'frio']);
            return;
        }

        if ($riesgo !== $this->etiqueta_riesgo) {
            $this->update(['etiqueta_riesgo' => $riesgo]);
        }
    }
}
