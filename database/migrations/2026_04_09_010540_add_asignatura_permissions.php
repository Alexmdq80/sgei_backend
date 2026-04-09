<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos para Asignaturas
        $permissions = [
            'asignaturas.ver',
            'asignaturas.gestionar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // 2. Asignar al Supervisor Curricular
        $supervisor = Role::findByName('supervisor_curricular', 'sanctum');
        if ($supervisor) {
            $supervisor->givePermissionTo(['asignaturas.ver', 'asignaturas.gestionar']);
        }

        // 3. Asignar al Superusuario
        $superuser = Role::findByName('superuser', 'sanctum');
        if ($superuser) {
            $superuser->givePermissionTo(['asignaturas.ver', 'asignaturas.gestionar']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', ['asignaturas.ver', 'asignaturas.gestionar'])->delete();
    }
};
