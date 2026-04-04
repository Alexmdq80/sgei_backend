<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Añadir la columna role_id si no existe
        if (!Schema::hasColumn('escuela_usuario', 'role_id')) {
            Schema::table('escuela_usuario', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('usuario_id');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }

        // 2. Migración de Datos
        if (Schema::hasTable('roles_escolares')) {
            $viejosRoles = DB::table('roles_escolares')->get();
            foreach ($viejosRoles as $viejo) {
                $nombreRole = strtolower($viejo->nombre);
                if ($nombreRole === 'administrador') $nombreRole = 'director';
                if ($nombreRole === 'personal') $nombreRole = 'profesor';

                $spatieRole = Role::where('name', $nombreRole)->first();
                if ($spatieRole) {
                    DB::table('escuela_usuario')
                        ->where('rol_escolar_id', $viejo->id)
                        ->update(['role_id' => $spatieRole->id]);
                }
            }
        }

        // 3. Limpieza de estructura vieja (Desactivamos FKs para evitar bloqueos en MySQL/SQLite)
        Schema::disableForeignKeyConstraints();

        if (Schema::hasColumn('escuela_usuario', 'rol_escolar_id')) {
            // Intentamos borrar FKs uno por uno en llamadas separadas
            try {
                Schema::table('escuela_usuario', function (Blueprint $table) {
                    $table->dropForeign(['rol_escolar_id']);
                });
            } catch (\Exception $e) {}

            try {
                Schema::table('escuela_usuario', function (Blueprint $table) {
                    $table->dropForeign('escuela_usuario_usuario_tipo_id_foreign');
                });
            } catch (\Exception $e) {}

            try {
                Schema::table('escuela_usuario', function (Blueprint $table) {
                    $table->dropForeign('escuela_usuario_rol_escolar_id_foreign');
                });
            } catch (\Exception $e) {}
            
            // Finalmente borramos la columna
            try {
                Schema::table('escuela_usuario', function (Blueprint $table) {
                    $table->dropColumn('rol_escolar_id');
                });
            } catch (\Exception $e) {}
        }

        Schema::dropIfExists('roles_escolares');
        
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void {}
};
