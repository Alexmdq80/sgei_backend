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
        Schema::create('cupofs', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cupof')->unique();
            $table->unsignedMediumInteger('escuela_id');
            $table->foreign('escuela_id')->references('id')->on('escuelas')->onDelete('cascade');
            $table->foreignId('asignatura_id')->nullable()->constrained('asignaturas')->onDelete('set null');
            $table->enum('escalafon', ['docente', 'auxiliar', 'administrativo'])->default('docente');
            $table->enum('tipo_puesto', ['cargo', 'horas_catedra', 'modulos'])->default('cargo');
            $table->integer('cantidad')->default(1);
            $table->enum('estado_cupof', ['disponible', 'ocupado', 'baja'])->default('disponible');
            $table->string('motivo_baja')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupofs');
    }
};
