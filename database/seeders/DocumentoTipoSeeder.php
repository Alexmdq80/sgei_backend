<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentoTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['id' => 1, 'nombre' => 'DNI', 'orden' => 1, 'vigente' => true],
            ['id' => 2, 'nombre' => 'PASAPORTE', 'orden' => 2, 'vigente' => true],
            ['id' => 3, 'nombre' => 'LE', 'orden' => 3, 'vigente' => true],
            ['id' => 4, 'nombre' => 'LC', 'orden' => 4, 'vigente' => true],
        ];

        foreach ($tipos as $tipo) {
            DB::table('documento_tipos')->updateOrInsert(
                ['id' => $tipo['id']],
                array_merge($tipo, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
