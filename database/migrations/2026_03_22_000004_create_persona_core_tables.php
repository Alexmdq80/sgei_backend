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
        Schema::create('vinculo_tipos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->tinyIncrements('id');
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vinculos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->tinyIncrements('id');
            $table->unsignedTinyInteger('vinculo_tipo_id');
            $table->foreign('vinculo_tipo_id')->references('id')->on('vinculo_tipos');
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('personas', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->unsignedTinyInteger('documento_tipo_id')->nullable();
            $table->foreign('documento_tipo_id')->references('id')->on('documento_tipos');
            $table->unsignedTinyInteger('documento_situacion_id')->nullable();
            $table->foreign('documento_situacion_id')->references('id')->on('documento_situacions');
            $table->unsignedTinyInteger('sexo_id')->nullable();
            $table->foreign('sexo_id')->references('id')->on('sexos');
            $table->unsignedTinyInteger('genero_id')->nullable();
            $table->foreign('genero_id')->references('id')->on('generos');
            $table->foreignId('nacionalidad_nacion_id')->nullable()->constrained('nacions');
            $table->foreignId('nacion_id')->nullable()->constrained('nacions'); // Pais de nacimiento
            $table->foreignId('provincia_id')->nullable()->constrained('provincias');
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->foreignId('localidad_id')->nullable()->constrained('localidads');
            $table->string('documento_numero')->nullable();
            $table->string('apellido');
            $table->string('nombre');
            $table->string('nombre_alternativo')->nullable();
            $table->string('tramite')->nullable();
            $table->boolean('posee_cpi_si')->default(false);
            $table->boolean('posee_docExt_si')->default(false);
            $table->boolean('vive_si')->default(true);
            $table->string('CUIL_prefijo', 2)->nullable();
            $table->string('CUIL_sufijo', 1)->nullable();
            $table->timestamp('nacimiento_fecha')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('persona_vinculo_persona', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('persona_estudiante_id')->constrained('personas');
            $table->foreignId('persona_adulto_id')->constrained('personas');
            $table->unsignedTinyInteger('vinculo_id');
            $table->foreign('vinculo_id')->references('id')->on('vinculos');
            $table->string('detalle')->nullable();
            $table->timestamp('vencimiento_fecha')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('domicilios', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('persona_id')->nullable()->constrained('personas');
            $table->foreignId('localidad_id')->nullable()->constrained('localidads');
            $table->foreignId('calle_id')->nullable()->constrained('calles');
            $table->foreignId('calle_entre_1_id')->nullable()->constrained('calles');
            $table->foreignId('calle_entre_2_id')->nullable()->constrained('calles');
            $table->string('numero')->nullable();
            $table->string('piso')->nullable();
            $table->string('torre')->nullable();
            $table->string('departamento')->nullable();
            $table->string('otros')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('contactos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('persona_id')->nullable()->constrained('personas');
            $table->string('telefono_codigo_area')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular_codigo_area')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('contactos');
        Schema::dropIfExists('domicilios');
        Schema::dropIfExists('persona_vinculo_persona');
        Schema::dropIfExists('personas');
        Schema::dropIfExists('vinculos');
        Schema::dropIfExists('vinculo_tipos');
    }
};
