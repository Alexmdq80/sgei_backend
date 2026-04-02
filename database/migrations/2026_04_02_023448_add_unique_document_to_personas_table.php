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
        Schema::table('personas', function (Blueprint $table) {
            // Añadir índice único para la combinación de Tipo + Número de Documento.
            // En SQL, múltiples valores NULL no violan la restricción UNIQUE, 
            // lo cual es útil para personas sin documento cargado aún.
            $table->unique(['documento_tipo_id', 'documento_numero'], 'personas_documento_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropUnique('personas_documento_unique');
        });
    }
};
