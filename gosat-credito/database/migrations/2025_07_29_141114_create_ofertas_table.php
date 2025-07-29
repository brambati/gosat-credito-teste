<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->onDelete('cascade');
            $table->string('cpf', 14);
            $table->integer('instituicao_id');
            $table->string('instituicao_nome');
            $table->string('modalidade_credito');
            $table->string('cod_modalidade');
            $table->decimal('valor_pagar', 12, 2);
            $table->decimal('valor_solicitado', 12, 2);
            $table->decimal('taxa_juros', 8, 4);
            $table->integer('qnt_parcelas');
            $table->decimal('score_vantagem', 8, 4)->comment('Score calculado para ordenação');
            $table->timestamps();
            
            $table->index(['cpf', 'score_vantagem']);
            $table->index('consulta_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas');
    }
};
