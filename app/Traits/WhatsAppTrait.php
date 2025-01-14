<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait WhatsAppTrait
{
    public static function enviarMensajeWhatsApp($phone_number, $message)
    {
        $data = array(
            'number' => $phone_number,
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

        $api_url = 'https://proyectos01-evo.5jfr60.easypanel.host/message/sendText/test';
        $headers = array(
            'Content-Type: application/json',
            'apikey: y618g7234oq21s2xjy3p78'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        $response = curl_exec($ch);

        if ($response === false) {
            Log::error('Error al enviar mensaje de WhatsApp: ' . curl_error($ch));
            return false;
        } else {
            Log::info('Mensaje enviado correctamente a WhatsApp: ' . $response);
            return true;
        }

        curl_close($ch);
    }
}