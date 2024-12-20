<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reparacion extends Model
{
    protected $fillable = [
        'descripcion',
        'servicios',
        'kilometraje',
        'notas',
        'cliente_id',
        'vehiculo_id',
        'empresa_id',
        'sucursal_id',
        'mecanico_id',
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

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function mecanico()
    {
        return $this->belongsTo(Mecanico::class);
    }
}
