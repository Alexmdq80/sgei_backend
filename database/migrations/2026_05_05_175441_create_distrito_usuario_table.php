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
        Schema::create('distrito_usuario', function (Blueprint $blueprint) {
            $blueprint->uuid('id')->primary();
            $blueprint->foreignUuid('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $blueprint->foreignId('departamento_id')->constrained('departamentos')->onDelete('cascade'); // El departamento representa el distrito
            $blueprint->timestamps();
            $blueprint->softDeletes();

            // Auditoría básica
            $blueprint->foreignUuid('created_by')->nullable()->constrained('usuarios');
            $blueprint->foreignUuid('updated_by')->nullable()->constrained('usuarios');
            $blueprint->foreignUuid('deleted_by')->nullable()->constrained('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distrito_usuario');
    }
};
