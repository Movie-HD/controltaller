<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mecanico extends Model
{
    protected $fillable = ['nombre', 'empresa_id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

}
