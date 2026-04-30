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
            ['id' => 1, 'nombre' => 'DNI', 'vigente' => true],
            ['id' => 2, 'nombre' => 'CDI', 'vigente' => true],
            ['id' => 3, 'nombre' => 'LC', 'vigente' => true],
            ['id' => 4, 'nombre' => 'PASAPORTE', 'vigente' => true],
            ['id' => 5, 'nombre' => 'OTRO', 'vigente' => true],
            ['id' => 6, 'nombre' => 'INDOCUMENTADO', 'vigente' => true],
        ];

        foreach ($tipos as $tipo) {
            DocumentoTipo::updateOrCreate(['id' => $tipo['id']], $tipo);
        }
    }
}
