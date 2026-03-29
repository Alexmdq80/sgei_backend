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
        // Tabla pivote singular segun modelo EscuelaModalidadNivel
        Schema::create('escuela_modalidad_nivel', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('escuela_id');
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->foreignId('modalidad_nivel_id')->constrained('modalidad_nivels');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Convencion Laravel: orden alfabetico para tabla pivote escuela_oferta
        Schema::create('escuela_oferta', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('escuela_id');
            $table->foreign('escuela_id')->references('id')->on('escuelas');
            $table->foreignId('oferta_id')->constrained('ofertas');
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
        Schema::dropIfExists('escuela_oferta');
        Schema::dropIfExists('escuela_modalidad_nivel');
    }
};
