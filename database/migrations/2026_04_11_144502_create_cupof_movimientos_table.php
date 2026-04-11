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
        Schema::create('cupof_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupof_id')->constrained('cupofs')->onDelete('cascade');
            $table->foreignId('agente_id')->constrained('agentes')->onDelete('cascade');
            $table->enum('situacion_revista', ['titular', 'provisional', 'suplente'])->default('provisional');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('resolucion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupof_movimientos');
    }
};
