<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Reparacion extends Model
{
    protected $fillable = [
        'descripcion',
        'servicios',
        'kilometraje',
        'notas',
        'precio',
        'cliente_id',
        'vehiculo_id',
        'empresa_id',
        'oportunidades',
        'estado',
        'position',
    ];

    protected $casts = [
        'oportunidades' => 'array',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function mecanicos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mecanico::class, 'mecanico_reparacion');
    }

    protected static function boot()
    {
        parent::boot();

        // Cuando se crea una nueva reparación
        static::created(function (Reparacion $reparacion) {
            $vehiculo = $reparacion->vehiculo;
            if ($vehiculo) {
                $vehiculo->update(['kilometraje' => $reparacion->kilometraje]);
            }
        });

        // Cuando se actualiza una reparación existente
        static::updated(function (Reparacion $reparacion) {
            $vehiculo = $reparacion->vehiculo;
            if ($vehiculo) {
                // Solo actualizamos si la reparación editada es la última registrada
                $ultimaReparacion = $vehiculo->reparaciones()->latest('created_at')->first();
                if ($ultimaReparacion && $ultimaReparacion->id === $reparacion->id) {
                    $vehiculo->update(['kilometraje' => $reparacion->kilometraje]);
                }
            }
        });
    }
    protected static function booted()
    {
        // Cuando se crea una reparación
        static::created(function ($reparacion) {
            // Broadcasting para el Kanban
            broadcast(new \App\Events\ReparacionActualizadaEvent($reparacion, 'creada'));

            // WhatsApp logic
            $vehiculo = $reparacion->vehiculo;
            $cliente = $vehiculo->cliente;
            $phone_number_cliente = '51' . $cliente->telefono;

            $message = "🔔 *Hola, {$cliente->nombre},*\n\n" .
                "Queremos informarte que hemos registrado una nueva reparación para tu vehículo *{$vehiculo->marca} {$vehiculo->modelo} ({$vehiculo->anio})*.\n\n" .
                "📝 *Detalles de la reparación:*\n" .
                "✔️ Placa: {$vehiculo->placa}\n" .
                "✔️ Descripción: {$reparacion->descripcion}\n" .
                "✔️ Kilometraje actual: {$reparacion->kilometraje} km\n\n" .
                "💡 Nos aseguraremos de que tu vehículo reciba el mejor cuidado. Si necesitas más información o tienes alguna consulta, no dudes en contactarnos.\n\n" .
                "🤝 ¡Gracias por confiar en nuestro taller!";

            self::enviar_mensaje_whatsapp($phone_number_cliente, $message);
        });

        // Cuando se actualiza una reparación
        static::updated(function (Reparacion $reparacion) {
            // Detectar si cambió de estado (movimiento en Kanban)
            if ($reparacion->isDirty('estado')) {
                $estadoAnterior = $reparacion->getOriginal('estado');
                $estadoNuevo = $reparacion->estado;

                if ($estadoAnterior !== $estadoNuevo) {
                    broadcast(new \App\Events\ReparacionMovidaEvent(
                        $reparacion,
                        $estadoAnterior,
                        $estadoNuevo
                    ));
                }
            }
            // Otros cambios
            else {
                broadcast(new \App\Events\ReparacionActualizadaEvent($reparacion, 'actualizada'));
            }
        });

        // Cuando se elimina una reparación
        static::deleted(function (Reparacion $reparacion) {
            broadcast(new \App\Events\ReparacionActualizadaEvent($reparacion, 'eliminada'));
        });
    }

    // Función para enviar el mensaje de WhatsApp
    public static function enviar_mensaje_whatsapp($phone_number, $message)
    {
        $data = array(
            'number' => $phone_number, // Número de destino
            'options' => array(
                'delay' => 1200,
                'presence' => 'composing',
                'linkPreview' => false
            ),
            'textMessage' => array(
                'text' => $message
            )
        );

        $json_data = json_encode($data);

        $api_url = 'https://proyectos01-evo.5jfr60.easypanel.host/message/sendText/test'; // URL de la API
        $headers = array(
            'Content-Type: application/json',
            'apikey: y618g7234oq21s2xjy3p78' // Tu API key aquí
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        // Ejecutar la solicitud cURL
        $response = curl_exec($ch);

        if ($response === false) {
            $error_message = curl_error($ch);
            Log::error('Error al enviar mensaje de WhatsApp: ' . $error_message);
        } else {
            Log::info('Mensaje enviado correctamente a WhatsApp: ' . $response);
        }

        curl_close($ch);
    }
}
