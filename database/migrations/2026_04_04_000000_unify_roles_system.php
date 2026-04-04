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

        // 3. Eliminar la columna vieja y su FK rebelde
        if (Schema::hasColumn('escuela_usuario', 'rol_escolar_id')) {
            Schema::table('escuela_usuario', function (Blueprint $table) {
                // SQLite no soporta borrar FKs por nombre, así que solo lo intentamos en otros drivers
                if (DB::getDriverName() !== 'sqlite') {
                    try {
                        $table->dropForeign('escuela_usuario_usuario_tipo_id_foreign');
                    } catch (\Exception $e) {}
                }
                
                $table->dropColumn('rol_escolar_id');
            });
        }

        Schema::dropIfExists('roles_escolares');
    }

    public function down(): void {}
};
