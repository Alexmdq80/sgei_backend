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
            // Eliminar campo obsoleto del flujo de vinculación manual
            if (Schema::hasColumn('usuarios', 'motivo_rechazo')) {
                $table->dropColumn('motivo_rechazo');
            }

            // Opcional: Podríamos simplificar el default de estado aquí si fuera necesario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->text('motivo_rechazo')->nullable()->after('estado');
        });
    }
};
