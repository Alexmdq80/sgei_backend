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
        Schema::table('cupofs', function (Blueprint $row) {
            // Añadir campo para el cargo específico (Director, Preceptor, etc.)
            $row->string('nombre_cargo')->nullable()->after('tipo_puesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cupofs', function (Blueprint $row) {
            $row->dropColumn('nombre_cargo');
        });
    }
};
