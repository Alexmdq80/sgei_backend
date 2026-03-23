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
        Schema::create('continentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('nacions', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('continente_id')->nullable()->constrained('continentes');
            $table->string('nombre');
            $table->string('nacionalidad')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('nacion_id')->nullable()->constrained('nacions');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->string('nombre');
            $table->string('nombre_completo')->nullable();
            $table->string('iso_nombre')->nullable();
            $table->string('iso_id')->nullable();
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('provincia_id')->nullable()->constrained('provincias');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->string('nombre');
            $table->string('nombre_completo')->nullable();
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->string('provincia_interseccion')->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->integer('distrito_numero')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('provincia_id')->nullable()->constrained('provincias');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->string('nombre');
            $table->string('nombre_completo')->nullable();
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->string('provincia_interseccion')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('localidad_censals', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->foreignId('georef_funcion_id')->nullable()->constrained('georef_funcions');
            $table->string('nombre');
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('localidads', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios');
            $table->foreignId('localidad_censal_id')->nullable()->constrained('localidad_censals');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->string('nombre');
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('georef_localidads', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios');
            $table->foreignId('localidad_censal_id')->nullable()->constrained('localidad_censals');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->string('nombre');
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('georef_asentamientos', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
            $table->foreignId('municipio_id')->nullable()->constrained('municipios');
            $table->foreignId('localidad_censal_id')->nullable()->constrained('localidad_censals');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
            $table->string('nombre');
            $table->decimal('centroide_lat', 10, 8)->nullable();
            $table->decimal('centroide_lon', 11, 8)->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('calles', function (Blueprint $table) {
            $table->id();
            $table->string('id_georef')->nullable();
            $table->string('nombre');
            $table->integer('altura_fin_derecha')->nullable();
            $table->integer('altura_fin_izquierda')->nullable();
            $table->integer('altura_inicio_derecha')->nullable();
            $table->integer('altura_inicio_izquierda')->nullable();
            $table->foreignId('localidad_censal_id')->nullable()->constrained('localidad_censals');
            $table->foreignId('georef_fuente_id')->nullable()->constrained('georef_fuentes');
            $table->foreignId('georef_categoria_id')->nullable()->constrained('georef_categorias');
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
        Schema::dropIfExists('calles');
        Schema::dropIfExists('georef_asentamientos');
        Schema::dropIfExists('georef_localidads');
        Schema::dropIfExists('localidads');
        Schema::dropIfExists('localidad_censals');
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('provincias');
        Schema::dropIfExists('nacions');
        Schema::dropIfExists('continentes');
    }
};
