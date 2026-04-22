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
        Schema::create('cargos', function (Blueprint $col) {
            $col->id();
            $col->string('nombre')->unique();
            $col->boolean('requiere_cursos')->default(false);
            $col->boolean('activo')->default(true);
            $col->uuid('created_by')->nullable();
            $col->uuid('updated_by')->nullable();
            $col->timestamps();
            $col->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};
