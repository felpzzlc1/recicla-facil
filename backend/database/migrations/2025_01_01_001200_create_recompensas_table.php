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
        Schema::create('recompensas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('icone')->nullable();
            $table->string('categoria');
            $table->string('categoria_icone')->nullable();
            $table->integer('pontos');
            $table->integer('disponivel')->default(0);
            $table->boolean('ativo')->default(true);
            $table->string('imagem_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recompensas');
    }
};
