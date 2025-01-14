<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehiculo extends Model
{
    protected $fillable = ['placa', 'marca', 'modelo', 'anio', 'color', 'km_registro', 'kilometraje', 'cliente_id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class);
    }

    public function whatsappmensajes()
    {
        return $this->hasMany(WhatsappMensaje::class);
    }

    public function getKilometrajeActualAttribute()
{
    // Obtener el último kilometraje de la última reparación asociada
    $lastRepair = $this->reparaciones()->latest('created_at')->first();

    // Si existe una reparación, retornamos su kilometraje, si no, retornamos el valor actual
    return $lastRepair ? $lastRepair->kilometraje : $this->km_registro;
}
}
