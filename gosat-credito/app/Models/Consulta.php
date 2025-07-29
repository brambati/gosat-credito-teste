<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consulta extends Model
{
    protected $fillable = [
        'cpf',
        'resposta_api',
        'total_ofertas'
    ];

    protected $casts = [
        'resposta_api' => 'array',
        'total_ofertas' => 'integer'
    ];

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }
}
