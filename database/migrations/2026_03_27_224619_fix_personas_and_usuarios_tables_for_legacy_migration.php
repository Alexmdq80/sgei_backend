<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function handle(): void
    {
        // Corregir tabla Usuarios
        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios', 'apellido')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('apellido')->nullable()->after('nombre');
            });
        }

        // Corregir tabla Personas (permitir NULL en campos de legados no obligatorios y cambiar tipo de fecha)
        if (Schema::hasTable('personas')) {
            Schema::table('personas', function (Blueprint $table) {
                $table->tinyInteger('posee_cpi_si')->nullable()->default(0)->change();
                $table->tinyInteger('posee_docExt_si')->nullable()->default(0)->change();
                $table->tinyInteger('vive_si')->nullable()->default(1)->change();
                $table->date('nacimiento_fecha')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios', 'apellido')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('apellido');
            });
        }

        if (Schema::hasTable('personas')) {
            Schema::table('personas', function (Blueprint $table) {
                $table->tinyInteger('posee_cpi_si')->nullable(false)->change();
                $table->tinyInteger('posee_docExt_si')->nullable(false)->change();
                $table->tinyInteger('vive_si')->nullable(false)->change();
            });
        }
    }
    
    // Sobrescribo up() por handle() por convención de Laravel 11/12+ si se usa, pero usaré up() por seguridad
    public function up(): void
    {
        $this->handle();
    }
};
