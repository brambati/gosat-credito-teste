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
        Schema::create('instituicoes', function (Blueprint $table) {
            $table->id();
            $table->integer('instituicao_id')->unique();
            $table->string('nome');
            $table->json('modalidades')->nullable();
            $table->timestamps();
            
            $table->index('instituicao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituicoes');
    }
};
