<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asignatura;
use App\Models\AnioPlan;
use Illuminate\Support\Facades\DB;

class AsignaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path('../asignaturas.csv'), 'r');
        
        // Skip header
        $header = fgetcsv($csvFile);
        
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($csvFile)) !== false) {
                $planId = $row[0];
                $anioAbsoluto = $row[2];
                
                // Find the correct AnioPlan
                $anioPlan = AnioPlan::where('plan_id', $planId)
                    ->whereHas('anio', function ($query) use ($anioAbsoluto) {
                        $query->where('anio_absoluto', $anioAbsoluto);
                    })
                    ->first();

                if (!$anioPlan) {
                    $this->command->warn("No se encontró AnioPlan para Plan ID: $planId y Año: $anioAbsoluto");
                    continue;
                }

                // Iterate through columns Materia_01 to Materia_12 (indices 3 to 14)
                for ($i = 3; $i <= 14; $i++) {
                    $nombreMateria = trim($row[$i] ?? '');
                    
                    if (empty($nombreMateria)) {
                        continue;
                    }

                    Asignatura::updateOrCreate([
                        'anio_plan_id' => $anioPlan->id,
                        'nombre' => $nombreMateria,
                    ], [
                        'nombre_completo' => $nombreMateria,
                        'orden' => $i - 2, // Simple ordering based on CSV column
                        'horas_semanales' => 4, // Default value if not specified
                    ]);
                }
            }
            DB::commit();
            $this->command->info('Asignaturas importadas exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al importar asignaturas: ' . $e->getMessage());
        }

        fclose($csvFile);
    }
}
