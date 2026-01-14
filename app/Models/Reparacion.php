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
        static::created(function ($reparacion) {
            // Aquí va el código para enviar el mensaje de WhatsApp

            // Obtener el número de teléfono del cliente (esto es solo un ejemplo, puedes ajustarlo según tu base de datos)
            $vehiculo = $reparacion->vehiculo; // Asegúrate de tener la relación correcta
            $cliente = $vehiculo->cliente; // Suponiendo que 'vehiculo' tiene una relación con 'cliente'

            $phone_number_cliente = '51' . $cliente->telefono; // Asegúrate de que 'telefono' sea el campo correcto

            $message = "🔔 *Hola, {$cliente->nombre},*\n\n" .
                "Queremos informarte que hemos registrado una nueva reparación para tu vehículo *{$vehiculo->marca} {$vehiculo->modelo} ({$vehiculo->anio})*.\n\n" .
                "📝 *Detalles de la reparación:*\n" .
                "✔️ Placa: {$vehiculo->placa}\n" .
                "✔️ Descripción: {$reparacion->descripcion}\n" .
                "✔️ Kilometraje actual: {$reparacion->kilometraje} km\n\n" .
                "💡 Nos aseguraremos de que tu vehículo reciba el mejor cuidado. Si necesitas más información o tienes alguna consulta, no dudes en contactarnos.\n\n" .
                "🤝 ¡Gracias por confiar en nuestro taller!";

            // Llamar a la función para enviar el mensaje
            self::enviar_mensaje_whatsapp($phone_number_cliente, $message);

            Log::info('Reparación creada y mensaje enviado por WhatsApp.', [
                'reparacion_id' => $reparacion->id,
                'cliente' => $cliente->nombre, // Asegúrate de tener el campo 'nombre' en tu modelo cliente
                'telefono_cliente' => $cliente->telefono
            ]);
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
