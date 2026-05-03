<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla de Escalafones
        Schema::create('escalafones', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nombre')->unique();
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Crear tabla de Tipos de Puesto
        Schema::create('puesto_tipos', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nombre')->unique();
            $table->integer('orden')->nullable();
            $table->boolean('vigente')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Poblar datos iniciales
        DB::table('escalafones')->insert([
            ['nombre' => 'DOCENTE', 'orden' => 1, 'vigente' => true, 'created_at' => now()],
            ['nombre' => 'AUXILIAR', 'orden' => 2, 'vigente' => true, 'created_at' => now()],
            ['nombre' => 'ADMINISTRATIVO', 'orden' => 3, 'vigente' => true, 'created_at' => now()],
        ]);

        DB::table('puesto_tipos')->insert([
            ['nombre' => 'CARGO', 'orden' => 1, 'vigente' => true, 'created_at' => now()],
            ['nombre' => 'HORAS CÁTEDRA', 'orden' => 2, 'vigente' => true, 'created_at' => now()],
            ['nombre' => 'MÓDULOS', 'orden' => 3, 'vigente' => true, 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto_tipos');
        Schema::dropIfExists('escalafones');
    }
};
