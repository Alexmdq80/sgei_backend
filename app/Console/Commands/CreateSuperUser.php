<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateSuperUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superuser {email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Convierte a un usuario existente en Super Administrador (Acceso Total)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            $this->error("No se encontró ningún usuario con el email: {$email}");
            return 1;
        }

        $this->info("Procesando usuario: {$usuario->nombre} ({$usuario->email})");

        // 1. Asignar flag de administrador
        $usuario->es_administrador = true;
        $usuario->save();

        // 2. Asignar rol de Spatie (superuser)
        $role = Role::where('name', 'superuser')->first();
        if ($role) {
            $usuario->assignRole($role);
            $this->info("Rol 'superuser' asignado correctamente.");
        } else {
            $this->warn("Advertencia: El rol 'superuser' no existe en la tabla de roles. Ejecute el seeder primero.");
        }

        $this->info("¡Éxito! El usuario ahora tiene privilegios de Super Administrador.");

        return 0;
    }
}
