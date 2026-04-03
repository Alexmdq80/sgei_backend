<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\DocumentoTipo;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener tipo de documento (DNI usualmente es ID 1)
        $docTipo = DocumentoTipo::find(1) ?? DocumentoTipo::first();
        if (!$docTipo) {
            $this->command->error('Error: Debe ejecutar DocumentoTipoSeeder antes para crear tipos de documento.');
            return;
        }

        // 2. Verificar rol superuser (usando guard sanctum como en RolesAndPermissionsSeeder)
        $role = Role::where('name', 'superuser')->where('guard_name', 'sanctum')->first();
        if (!$role) {
            $this->command->error('Error: Debe ejecutar RolesAndPermissionsSeeder antes para crear los roles.');
            return;
        }

        $email = config('app.admin_email') ?? 'admin@sgei.local';
        $password = config('app.admin_pass') ?? 'Sgei!2026_Admin';

        // 3. Crear o actualizar el administrador del sistema
        $admin = Usuario::updateOrCreate(
            ['email' => $email],
            [
                'nombre' => 'Administrador',
                'documento_tipo_id' => $docTipo->id,
                'documento_numero' => '00000000',
                'es_administrador' => true,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        // 4. Asignar el rol de superuser si no lo tiene
        if (!$admin->hasRole($role)) {
            $admin->assignRole($role);
        }

        $this->command->info("✅ Administrador del Sistema sembrado con éxito: {$email}");
        Log::info("AdminSistemaSeeder: Usuario administrador creado/actualizado: {$email}");
    }
}
