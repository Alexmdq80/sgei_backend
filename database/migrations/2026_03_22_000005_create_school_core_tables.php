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
        Schema::create('usuario_tipos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->tinyIncrements('id'); // Ajustado a tinyint para el respaldo
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('escuela_tipos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->tinyIncrements('id'); // Ajustado a tinyint
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('ofertas', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('modalidad_nivels', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedTinyInteger('nivel_id');
            $table->foreign('nivel_id')->references('id')->on('nivels');
            $table->unsignedTinyInteger('modalidad_id');
            $table->foreign('modalidad_id')->references('id')->on('modalidads');
            $table->unsignedTinyInteger('escuela_tipo_id')->nullable();
            $table->foreign('escuela_tipo_id')->references('id')->on('escuela_tipos');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('escuelas', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->mediumIncrements('id'); // Ajustado a mediumint para el respaldo
            $table->foreignId('localidad_id')->nullable()->constrained('localidads');
            $table->unsignedTinyInteger('ambito_id')->nullable();
            $table->foreign('ambito_id')->references('id')->on('ambitos');
            $table->unsignedTinyInteger('dependencia_id')->nullable();
            $table->foreign('dependencia_id')->references('id')->on('dependencias');
            $table->unsignedTinyInteger('sector_id')->nullable();
            $table->foreign('sector_id')->references('id')->on('sectors');
            $table->string('cue_anexo')->nullable();
            $table->string('clave_provincial')->nullable();
            $table->string('nombre');
            $table->string('numero')->nullable();
            $table->string('codigo_localidad')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->boolean('modalidad_comun')->default(false);
            $table->boolean('modalidad_especial')->default(false);
            $table->boolean('modalidad_adultos')->default(false);
            $table->boolean('comun_inicial_maternal')->default(false);
            $table->boolean('comun_inicial_infantes')->default(false);
            $table->boolean('comun_primario')->default(false);
            $table->boolean('comun_secundario')->default(false);
            $table->boolean('comun_secundario_inet')->default(false);
            $table->boolean('comun_snu')->default(false);
            $table->boolean('comun_snu_inet')->default(false);
            $table->boolean('comun_snu_cursos')->default(false);
            $table->boolean('especial_temprana')->default(false);
            $table->boolean('especial_inicial')->default(false);
            $table->boolean('especial_primario')->default(false);
            $table->boolean('especial_secundario')->default(false);
            $table->boolean('especial_integracion')->default(false);
            $table->boolean('adultos_primario')->default(false);
            $table->boolean('adultos_secundario')->default(false);
            $table->boolean('adultos_profesional')->default(false);
            $table->boolean('adultos_profesional_inet')->default(false);
            $table->boolean('adultos_alfabetizacion')->default(false);
            $table->boolean('hospitalario_inicial')->default(false);
            $table->boolean('hospitalario_primario')->default(false);
            $table->boolean('hospitalario_secundario')->default(false);
            $table->boolean('talleres_artistica')->default(false);
            $table->boolean('servicios_complementarios')->default(false);
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
        Schema::dropIfExists('escuelas');
        Schema::dropIfExists('modalidad_nivels');
        Schema::dropIfExists('ofertas');
        Schema::dropIfExists('escuela_tipos');
        Schema::dropIfExists('usuario_tipos');
    }
};
