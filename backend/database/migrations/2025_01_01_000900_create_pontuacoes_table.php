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
        Schema::create('pontuacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('pontos')->default(0);
            $table->integer('nivel')->default(1);
            $table->string('nivel_nome')->default('Iniciante');
            $table->integer('descartes')->default(0);
            $table->integer('sequencia_dias')->default(0);
            $table->integer('badges_conquistadas')->default(0);
            $table->integer('pontos_semana_atual')->default(0);
            $table->integer('total_pontos_ganhos')->default(0);
            $table->timestamp('ultima_atualizacao')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'pontos']);
            $table->index('nivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pontuacoes');
    }
};
