<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coletas', function (Blueprint $table) {
            $table->id();
            $table->string('material');
            $table->decimal('quantidade', 10, 2);
            $table->string('endereco');
            $table->date('data_preferida');
            $table->text('obs')->nullable();
            $table->enum('status', ['ABERTA', 'CONCLUIDA'])->default('ABERTA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coletas');
    }
};


