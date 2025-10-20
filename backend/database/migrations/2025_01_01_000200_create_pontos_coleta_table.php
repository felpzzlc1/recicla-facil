<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ponto_coletas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('tipo');
            $table->string('endereco');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ponto_coletas');
    }
};


