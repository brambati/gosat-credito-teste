<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Oferta extends Model
{
    protected $fillable = [
        'consulta_id',
        'cpf',
        'instituicao_id',
        'instituicao_nome',
        'modalidade_credito',
        'cod_modalidade',
        'valor_pagar',
        'valor_solicitado',
        'taxa_juros',
        'qnt_parcelas',
        'score_vantagem'
    ];

    protected $casts = [
        'consulta_id' => 'integer',
        'instituicao_id' => 'integer',
        'valor_pagar' => 'decimal:2',
        'valor_solicitado' => 'decimal:2',
        'taxa_juros' => 'decimal:4',
        'qnt_parcelas' => 'integer',
        'score_vantagem' => 'decimal:4'
    ];

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function instituicao(): BelongsTo
    {
        return $this->belongsTo(Instituicao::class, 'instituicao_id', 'instituicao_id');
    }
}
