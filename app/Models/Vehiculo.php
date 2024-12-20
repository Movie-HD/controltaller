<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehiculo extends Model
{
    protected $fillable = ['placa', 'marca', 'modelo', 'anio', 'color', 'kilometraje', 'cliente_id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
