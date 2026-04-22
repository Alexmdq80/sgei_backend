<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cargo;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            ['nombre' => 'Preceptor/a', 'requiere_cursos' => true],
            ['nombre' => 'EMATP', 'requiere_cursos' => false],
            ['nombre' => 'Bibliotecario/a', 'requiere_cursos' => false],
            ['nombre' => 'Secretario/a', 'requiere_cursos' => false],
            ['nombre' => 'Prosecretario/a', 'requiere_cursos' => false],
            ['nombre' => 'Vicedirector/a', 'requiere_cursos' => false],
            ['nombre' => 'Director/a', 'requiere_cursos' => false],
            ['nombre' => 'Jefe de Departamento', 'requiere_cursos' => false],
        ];

        foreach ($cargos as $cargo) {
            Cargo::updateOrCreate(
                ['nombre' => mb_strtoupper($cargo['nombre'], 'UTF-8')],
                ['requiere_cursos' => $cargo['requiere_cursos'], 'activo' => true]
            );
        }
    }
}
