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
        DB::table('documento_tipos')->insert([
            ['id' => 1, 'nombre' => 'DNI', 'orden' => 1, 'vigente' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'PASAPORTE', 'orden' => 2, 'vigente' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'LE', 'orden' => 3, 'vigente' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'LC', 'orden' => 4, 'vigente' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
