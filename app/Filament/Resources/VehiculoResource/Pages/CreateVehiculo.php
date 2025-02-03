<?php

namespace App\Filament\Resources\VehiculoResource\Pages;

use App\Filament\Resources\VehiculoResource;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateVehiculo extends CreateRecord
{
    protected static string $resource = VehiculoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record->id]) . '?activeRelationManager=0';
    }
    protected function handleRecordCreation(array $data): Model
{

    // Recoger los datos necesarios
    $cliente_id = $data['cliente_id'];
    $placa = $data['placa'];
    $marca = $data['marca'];
    $modelo = $data['modelo'];
    $anio = $data['anio'];
    $color = $data['color'];
    $kilometraje = $data['km_registro'];

    // Obtener los detalles del cliente (suponiendo que tienes una relación con el cliente)
    $cliente = Cliente::find($cliente_id);

    // Crear el mensaje de bienvenida
    $message = "🔧 *¡Hola, {$cliente->nombre}!*\n\n" .
           "Estamos encantados de recibir tu vehículo *{$marca} {$modelo} ({$anio})* en nuestro taller.\n\n" .
           "📝 *Detalles del tu vehículo:*\n" .
           "🚘 *Placa:* {$placa}\n" .
           "🎨 *Color:* {$color}\n" .
           "📏 *Kilometraje:* {$kilometraje} km\n\n" .
           "🤝 Nuestro equipo está listo para brindarte el mejor servicio y cuidar de tu vehículo como si fuera nuestro. Te mantendremos informado sobre cualquier novedad.\n\n" .
           "📞 Si tienes alguna consulta, no dudes en contactarnos. ¡Gracias por elegirnos!";


    // Número de teléfono del cliente (asumiendo que tienes el número en el modelo de Cliente)
    $phone_number_cliente = '51' . $cliente->telefono; // O usa el número de teléfono del cliente según tu base de datos

    // Llamar a la función para enviar el mensaje de WhatsApp
    self::enviar_mensaje_whatsapp($phone_number_cliente, $message);

    // Aquí se guardaría el vehículo en la base de datos (esto depende de tu implementación actual)
    return Vehiculo::create($data);
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
