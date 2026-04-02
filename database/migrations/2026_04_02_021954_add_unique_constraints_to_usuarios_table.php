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
        Schema::table('usuarios', function (Blueprint $table) {
            // 1. Hacer el nombre (alias) único
            // Nota: change() requiere doctrine/dbal pero en Laravel 11/13 ya no es obligatorio.
            // Aseguramos que sea único.
            $table->string('nombre')->unique()->change();

            // 2. Hacer la combinación Tipo + Número de Documento única
            // Para evitar el error de MySQL de "needed in a foreign key constraint",
            // primero creamos el nuevo índice único y luego intentamos borrar el viejo.
            $table->unique(['documento_tipo_id', 'documento_numero'], 'usuarios_documento_unique');
        });

        Schema::table('usuarios', function (Blueprint $table) {
            // Ahora que el nuevo índice cubre la llave foránea, borramos el viejo
            $table->dropIndex(['documento_tipo_id', 'documento_numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->index(['documento_tipo_id', 'documento_numero']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
            $table->dropUnique('usuarios_documento_unique');
        });
    }
};
