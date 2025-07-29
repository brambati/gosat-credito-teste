<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instituicao extends Model
{
    protected $table = 'instituicoes';

    protected $fillable = [
        'instituicao_id',
        'nome',
        'modalidades'
    ];

    protected $casts = [
        'instituicao_id' => 'integer',
        'modalidades' => 'array'
    ];

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class, 'instituicao_id', 'instituicao_id');
    }
}
