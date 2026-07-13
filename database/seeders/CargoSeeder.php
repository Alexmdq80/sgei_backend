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
        // escalafon_id: 1=DOCENTE, 2=AUXILIAR, 3=ADMINISTRATIVO
        // tipo: 'cargo' | 'horas' | 'modulos'
        $cargos = [
            ['nombre' => 'Director/a',          'tipo' => 'cargo', 'escalafon_id' => 1, 'requiere_cursos' => false],
            ['nombre' => 'Vicedirector/a',       'tipo' => 'cargo', 'escalafon_id' => 1, 'requiere_cursos' => false],
            ['nombre' => 'Secretario/a',         'tipo' => 'cargo', 'escalafon_id' => 3, 'requiere_cursos' => false],
            ['nombre' => 'Prosecretario/a',      'tipo' => 'cargo', 'escalafon_id' => 3, 'requiere_cursos' => false],
            ['nombre' => 'Preceptor/a',          'tipo' => 'cargo', 'escalafon_id' => 1, 'requiere_cursos' => true],
            ['nombre' => 'EMATP',                'tipo' => 'cargo', 'escalafon_id' => 1, 'requiere_cursos' => false],
            ['nombre' => 'Bibliotecario/a',      'tipo' => 'cargo', 'escalafon_id' => 2, 'requiere_cursos' => false],
            ['nombre' => 'Jefe de Departamento', 'tipo' => 'cargo', 'escalafon_id' => 1, 'requiere_cursos' => false],
        ];

        foreach ($cargos as $cargo) {
            Cargo::updateOrCreate(
                ['nombre' => mb_strtoupper($cargo['nombre'], 'UTF-8')],
                [
                    'tipo'          => $cargo['tipo'],
                    'escalafon_id'  => $cargo['escalafon_id'],
                    'requiere_cursos' => $cargo['requiere_cursos'],
                    'activo'        => true,
                ]
            );
        }
    }
}
