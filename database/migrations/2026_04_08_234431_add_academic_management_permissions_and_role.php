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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos Académicos
        $permissions = [
            'planes.ver',
            'planes.gestionar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // 2. Crear Rol Supervisor Curricular
        $supervisor = Role::firstOrCreate(['name' => 'supervisor_curricular', 'guard_name' => 'sanctum']);

        // 3. Asignar Permisos al Supervisor Curricular
        Permission::firstOrCreate(['name' => 'institucion.ver', 'guard_name' => 'sanctum']);
        
        $supervisor->givePermissionTo([
            'planes.ver',
            'planes.gestionar',
            'institucion.ver', // Lectura de escuelas para contexto
        ]);

        // 4. Asignar nuevos permisos al Superusuario
        $superuser = Role::firstOrCreate(['name' => 'superuser', 'guard_name' => 'sanctum']);
        $superuser->givePermissionTo(['planes.ver', 'planes.gestionar']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // El rollback de roles/permisos suele ser manual o destructivo, 
        // pero por seguridad aquí solo removemos el rol y permisos creados.
        Permission::whereIn('name', ['planes.ver', 'planes.gestionar'])->delete();
        Role::where('name', 'supervisor_curricular')->delete();
    }
};
