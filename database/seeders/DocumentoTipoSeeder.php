<?php

namespace Database\Seeders;

use App\Models\DocumentoTipo;
use Illuminate\Database\Seeder;

class DocumentoTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['id' => 1, 'nombre' => 'DNI', 'orden' => 1, 'vigente' => true],
            ['id' => 2, 'nombre' => 'CDI', 'orden' => 2, 'vigente' => true],
            ['id' => 3, 'nombre' => 'LC', 'orden' => 3, 'vigente' => true],
            ['id' => 4, 'nombre' => 'PASAPORTE', 'orden' => 4, 'vigente' => true],
            ['id' => 5, 'nombre' => 'OTRO', 'orden' => 5, 'vigente' => true],
            ['id' => 6, 'nombre' => 'INDOCUMENTADO', 'orden' => 6, 'vigente' => true],
        ];

        foreach ($tipos as $tipo) {
            DocumentoTipo::updateOrCreate(['id' => $tipo['id']], $tipo);
        }
    }
}
