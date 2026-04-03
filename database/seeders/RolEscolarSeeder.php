<?php

namespace Database\Seeders;

use App\Models\RolEscolar;
use Illuminate\Database\Seeder;

class RolEscolarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Director', 'orden' => 1, 'vigente' => true],
            ['nombre' => 'Secretario', 'orden' => 2, 'vigente' => true],
            ['nombre' => 'Preceptor', 'orden' => 3, 'vigente' => true],
            ['nombre' => 'Administrador', 'orden' => 4, 'vigente' => true],
            ['nombre' => 'Personal', 'orden' => 5, 'vigente' => true],
        ];

        foreach ($roles as $role) {
            RolEscolar::updateOrCreate(
                ['nombre' => $role['nombre']],
                $role
            );
        }
    }
}
