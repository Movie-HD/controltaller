<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappPlantilla extends Model
{
    protected $fillable = [
        'nombre',
        'mensaje'
    ];
}
