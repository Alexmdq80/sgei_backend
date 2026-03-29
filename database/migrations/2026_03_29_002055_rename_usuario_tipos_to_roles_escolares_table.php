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
        Schema::rename('usuario_tipos', 'roles_escolares');
        
        Schema::table('escuela_usuario', function (Blueprint $table) {
            $table->renameColumn('usuario_tipo_id', 'rol_escolar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escuela_usuario', function (Blueprint $table) {
            $table->renameColumn('rol_escolar_id', 'usuario_tipo_id');
        });

        Schema::rename('roles_escolares', 'usuario_tipos');
    }
};
