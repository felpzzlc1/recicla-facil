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
        Schema::create('resgate_recompensas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recompensa_id')->constrained('recompensas')->onDelete('cascade');
            $table->integer('pontos_gastos');
            $table->enum('status', ['PENDENTE', 'APROVADO', 'REJEITADO', 'ENTREGUE'])->default('PENDENTE');
            $table->timestamp('data_resgate');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resgate_recompensas');
    }
};
