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
        Schema::create('cronograma_coletas', function (Blueprint $table) {
            $table->id();
            $table->string('material'); // Papel, Plástico, Metal, Vidro, etc.
            $table->string('dia_semana'); // Segunda-feira, Terça-feira, etc.
            $table->time('horario_inicio'); // 08:00:00
            $table->time('horario_fim'); // 12:00:00
            $table->string('endereco'); // Endereço da coleta
            $table->string('bairro'); // Bairro da coleta
            $table->string('cidade'); // Cidade da coleta
            $table->string('estado'); // Estado da coleta
            $table->decimal('latitude', 10, 8)->nullable(); // Coordenada latitude
            $table->decimal('longitude', 11, 8)->nullable(); // Coordenada longitude
            $table->text('observacoes')->nullable(); // Observações adicionais
            $table->boolean('ativo')->default(true); // Se o cronograma está ativo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cronograma_coletas');
    }
};
