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
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre_completo')->nullable();
            $table->foreignId('anio_plan_id')->constrained('anio_plan')->onDelete('cascade');
            $table->integer('horas_semanales')->default(0);
            $table->string('codigo')->nullable();
            $table->integer('orden')->default(0);
            
            // Auditoría y Sistema
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
        Schema::dropIfExists('asignaturas');
    }
};
