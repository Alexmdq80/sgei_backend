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
        Schema::create('agentes', function (Blueprint $便利) {
            $便利->id();
            $便利->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            $便利->string('legajo')->unique()->nullable();
            $便利->date('fecha_ingreso_sistema')->nullable();
            $便利->enum('estado_administrativo', ['activo', 'licencia', 'jubilado', 'baja'])->default('activo');
            $便利->timestamps();
            $便利->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agentes');
    }
};
