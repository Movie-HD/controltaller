<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'empresa_id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }
}
