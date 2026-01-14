<?php
// app/Models/Compra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\CompraRegistradaEvent;

class Compra extends Model
{
    protected $fillable = [
        'cliente_id',
        'monto',
        'productos',
        'metodo_pago',
        'vendedor_id',
        'notas',
        'estado_cliente_en_compra',
        'fecha_compra',
    ];

    protected $casts = [
        'productos' => 'array',
        'monto' => 'decimal:2',
        'fecha_compra' => 'datetime',
    ];

    // RELACIONES
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // EVENTOS
    protected static function booted(): void
    {
        static::created(function (Compra $compra) {
            $cliente = $compra->cliente;

            // Actualizar fechas
            if (!$cliente->fecha_primera_compra) {
                $cliente->fecha_primera_compra = $compra->fecha_compra;

                if ($cliente->estado === 'curioso') {
                    $cliente->origen = 'curioso_convertido';
                    $cliente->estado = 'primerizo';
                }
            }

            $cliente->fecha_ultima_compra = $compra->fecha_compra;
            $cliente->save();

            // Recalcular métricas
            $cliente->actualizarMetricas();

            // 🔥 BROADCAST: Nueva compra registrada
            broadcast(new CompraRegistradaEvent($compra, $cliente));
        });
    }
}
