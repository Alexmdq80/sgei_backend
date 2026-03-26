<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::firstOrCreate(['name' => 'manage-users', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'view-reports', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'manage-schools', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'edit-own-profile', 'guard_name' => 'sanctum']);
        
        // Create Roles and Assign Permissions
        $superUserRole = Role::firstOrCreate(['name' => 'superuser', 'guard_name' => 'sanctum']);
        $adminFullRole = Role::firstOrCreate(['name' => 'admin_full', 'guard_name' => 'sanctum']);
        $adminStandardRole = Role::firstOrCreate(['name' => 'admin_standard', 'guard_name' => 'sanctum']);

        // Superuser gets all permissions (or you can assign them explicitly)
        // For simplicity, we'll assign all existing permissions to superuser role
        $superUserRole->givePermissionTo(Permission::all());

        // Admin Full Role Permissions
        $adminFullRole->givePermissionTo(['manage-users', 'view-reports', 'manage-schools', 'edit-own-profile']);

        // Admin Standard Role Permissions
        $adminStandardRole->givePermissionTo(['view-reports', 'edit-own-profile']);

        // Create a Superuser
        $superUser = Usuario::firstOrCreate(
            ['email' => 'superuser@example.com'],
            [
                'nombre' => 'Super',
                'documento_tipo_id' => 1,
                'documento_numero' => '99999999',
                'es_administrador' => true,
                'password' => Hash::make('password'), // Set a default password
                'email_verified_at' => now(),
            ]
        );
        $superUser->assignRole($superUserRole);

        // Create an Admin Full User
        $adminFullUser = Usuario::firstOrCreate(
            ['email' => 'adminfull@example.com'],
            [
                'nombre' => 'Admin',
                'documento_tipo_id' => 1,
                'documento_numero' => '88888888',
                'es_administrador' => true,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminFullUser->assignRole($adminFullRole);

        // Create an Admin Standard User
        $adminStandardUser = Usuario::firstOrCreate(
            ['email' => 'adminstandard@example.com'],
            [
                'nombre' => 'Admin',
                'documento_tipo_id' => 1,
                'documento_numero' => '77777777',
                'es_administrador' => true,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminStandardUser->assignRole($adminStandardRole);
    }
}
