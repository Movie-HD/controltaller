<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMensaje extends Model
{
    protected $fillable = [
        'mensaje',
        'fecha_programada',
        'estado',
        'cliente_id',
        'vehiculo_id',
        'reparacion_id',
        'plantilla_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    public function plantilla()
    {
        return $this->belongsTo(WhatsappPlantilla::class);
    }
}
