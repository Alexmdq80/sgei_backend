<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creamos un usuario de prueba para el frontend
        Usuario::updateOrCreate(
            ['email' => 'admin@sgei.com'],
            [
                'nombre' => 'Admin',
                'documento_tipo_id' => 1,
                'documento_numero' => '11111111',
                'password' => Hash::make('admin1234'),
                'email_verified_at' => now(),
            ]
        );
    }
}
