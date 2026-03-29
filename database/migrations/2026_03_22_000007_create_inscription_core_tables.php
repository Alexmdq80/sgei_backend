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
        Schema::create('salida_motivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cierre_causas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('condicions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('seccion_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('escuela_ubicacions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('espacios', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('propuesta_id')->constrained('propuestas');
            $table->foreignId('seccion_tipo_id')->constrained('seccion_tipos');
            $table->string('division')->nullable();
            $table->string('division_nombre')->nullable();
            $table->string('nombre')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('legajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas');
            $table->unsignedMediumInteger('escuela_id');
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->string('libro')->nullable();
            $table->string('folio')->nullable();
            $table->string('legajo')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('inscripcions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('persona_id')->constrained('personas');
            $table->foreignId('persona_firma_id')->nullable()->constrained('personas');
            $table->foreignId('espacio_id')->nullable()->constrained('espacios');
            $table->unsignedMediumInteger('escuela_id')->nullable();
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->unsignedTinyInteger('nivel_id')->nullable();
            $table->foreign('nivel_id')->references('id')->on('nivels');
            $table->unsignedTinyInteger('modalidad_id')->nullable();
            $table->foreign('modalidad_id')->references('id')->on('modalidads');
            $table->foreignId('condicion_id')->nullable()->constrained('condicions');
            $table->foreignId('persona_vinculo_persona_1_id')->nullable()->constrained('persona_vinculo_persona');
            $table->foreignId('persona_vinculo_persona_2_id')->nullable()->constrained('persona_vinculo_persona');
            $table->foreignId('persona_vinculo_persona_3_id')->nullable()->constrained('persona_vinculo_persona');
            $table->string('codigo_abc')->nullable();
            $table->boolean('proyecto_inclusion_si')->default(false);
            $table->boolean('concurre_especial_si')->default(false);
            $table->boolean('asistente_externo_si')->default(false);
            $table->timestamp('fecha')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('historial_inscripcions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('inscripcion_id')->constrained('inscripcions');
            $table->foreignId('persona_id')->constrained('personas');
            $table->foreignId('persona_firma_id')->nullable()->constrained('personas');
            $table->foreignId('espacio_id')->nullable()->constrained('espacios');
            $table->unsignedMediumInteger('escuela_id')->nullable();
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->unsignedTinyInteger('nivel_id')->nullable();
            $table->foreign('nivel_id')->references('id')->on('nivels');
            $table->unsignedTinyInteger('modalidad_id')->nullable();
            $table->foreign('modalidad_id')->references('id')->on('modalidads');
            $table->foreignId('condicion_id')->nullable()->constrained('condicions');
            $table->foreignId('persona_vinculo_persona_1_id')->nullable()->constrained('persona_vinculo_persona');
            $table->foreignId('persona_vinculo_persona_2_id')->nullable()->constrained('persona_vinculo_persona');
            $table->foreignId('persona_vinculo_persona_3_id')->nullable()->constrained('persona_vinculo_persona');
            $table->string('codigo_abc')->nullable();
            $table->boolean('proyecto_inclusion_si')->default(false);
            $table->boolean('concurre_especial_si')->default(false);
            $table->boolean('asistente_externo_si')->default(false);
            $table->timestamp('fecha')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('historial_info_inscripcions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historial_inscripcion_id')->constrained('historial_inscripcions');
            $table->foreignId('cierre_causa_id')->nullable()->constrained('cierre_causas');
            $table->timestamp('fecha')->nullable();
            $table->text('observaciones')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('inscripcion_pases', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('escuela_id');
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->foreignId('historial_inscripcion_id')->constrained('historial_inscripcions');
            $table->foreignId('salida_motivo_id')->nullable()->constrained('salida_motivos');
            $table->foreignId('escuela_ubicacion_id')->nullable()->constrained('escuela_ubicacions');
            $table->string('otro_motivo')->nullable();
            $table->boolean('finalizado')->default(false);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('inscripcion_bajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historial_inscripcion_id')->constrained('historial_inscripcions');
            $table->foreignId('salida_motivo_id')->nullable()->constrained('salida_motivos');
            $table->string('otro_motivo')->nullable();
            $table->boolean('accion_contacto')->default(false);
            $table->boolean('accion_prevencion')->default(false);
            $table->boolean('accion_equipo')->default(false);
            $table->boolean('accion_otros')->default(false);
            $table->boolean('accion_ninguna')->default(false);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('inscripcion_finalizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historial_inscripcion_id')->constrained('historial_inscripcions');
            $table->foreignId('condicion_id')->nullable()->constrained('condicions');
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
        Schema::dropIfExists('inscripcion_finalizados');
        Schema::dropIfExists('inscripcion_bajas');
        Schema::dropIfExists('inscripcion_pases');
        Schema::dropIfExists('historial_info_inscripcions');
        Schema::dropIfExists('historial_inscripcions');
        Schema::dropIfExists('inscripcions');
        Schema::dropIfExists('legajos');
        Schema::dropIfExists('espacios');
        Schema::dropIfExists('escuela_ubicacions');
        Schema::dropIfExists('seccion_tipos');
        Schema::dropIfExists('condicions');
        Schema::dropIfExists('cierre_causas');
        Schema::dropIfExists('salida_motivos');
    }
};
