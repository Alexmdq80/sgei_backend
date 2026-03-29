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
        Schema::create('lectivos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->string('nombre');
            $table->integer('anio')->nullable();
            $table->integer('orden')->nullable();
            $table->boolean('cerrado')->default(false);
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('anios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre_completo')->nullable();
            $table->integer('anio_absoluto')->nullable();
            $table->integer('anio_relativo')->nullable();
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('plan_ciclos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_ciclo_id')->nullable()->constrained('plan_ciclos');
            $table->string('nombre');
            $table->text('nombre_completo')->nullable();
            $table->integer('duracion_anios')->nullable();
            $table->string('resolucion')->nullable();
            $table->string('orientacion')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('anio_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans');
            $table->foreignId('anio_id')->constrained('anios');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('propuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('escuela_id');
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->foreignId('anio_plan_id')->constrained('anio_plan');
            $table->unsignedTinyInteger('turno_inicio_id')->nullable();
            $table->foreign('turno_inicio_id')->references('id')->on('turnos');
            $table->unsignedTinyInteger('turno_fin_id')->nullable();
            $table->foreign('turno_fin_id')->references('id')->on('turnos');
            $table->unsignedTinyInteger('jornada_id')->nullable();
            $table->foreign('jornada_id')->references('id')->on('jornadas');
            $table->foreignId('lectivo_id')->nullable()->constrained('lectivos');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propuestas');
        Schema::dropIfExists('anio_plan');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('plan_ciclos');
        Schema::dropIfExists('anios');
        Schema::dropIfExists('lectivos');
    }
};
