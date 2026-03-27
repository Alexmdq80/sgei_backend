<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sgei:migrate-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra datos de la base de datos antigua a la nueva estructura.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--- INICIANDO MIGRACIÓN DE DATOS LEGADOS ---');

        // Desactivar restricciones de claves foráneas
        Schema::disableForeignKeyConstraints();

        $tablesToMigrate = [
            'documento_tipos',
            'ambitos',
            'anios',
            'vinculo_tipos',
            'generos',
            'continentes',
            'naciones',
            'provincias',
            'departamentos',
            'municipios',
            'localidades',
            'escuela_tipos',
            'niveles',
            'modalidades',
            'jornadas',
            'ofertas',
            'seccion_tipos',
            'planes',
            'lectivos',
            'espacios',
            'legajos',
            'escuelas',
            'escuela_modalidad_nivel',
            'escuela_oferta',
            'domicilios',
            'contactos',
            'personas',
            'usuarios',
        ];

        foreach ($tablesToMigrate as $tableName) {
            $this->migrateTable($tableName);
        }

        // Reactivar restricciones de claves foráneas
        Schema::enableForeignKeyConstraints();

        $this->info('--- MIGRACIÓN COMPLETADA EXITOSAMENTE ---');
    }

    /**
     * Migra una tabla específica de legacy a default.
     * 
     * @param string $tableName
     */
    private function migrateTable(string $tableName): void
    {
        $this->comment("Migrando tabla: {$tableName}...");

        try {
            // Verificar si la tabla existe en legacy
            if (!Schema::connection('legacy')->hasTable($tableName)) {
                $this->warn("La tabla '{$tableName}' no existe en la base de datos legacy.");
                return;
            }

            // Obtener datos de la base legacy
            $legacyData = DB::connection('legacy')->table($tableName)->get();

            if ($legacyData->isEmpty()) {
                $this->line("La tabla '{$tableName}' está vacía.");
                return;
            }

            // Limpiar tabla destino antes de insertar
            DB::table($tableName)->truncate();

            // Insertar datos en la base default
            // Convertimos la colección de objetos a arrays
            $dataToInsert = $legacyData->map(function ($item) {
                return (array) $item;
            })->toArray();

            // Insertar en bloques para evitar problemas de memoria
            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                DB::table($tableName)->insert($chunk);
            }

            $count = count($dataToInsert);
            $this->info("✓ Se migraron {$count} registros en '{$tableName}'.");

        } catch (\Exception $e) {
            $this->error("Error al migrar la tabla '{$tableName}': " . $e->getMessage());
        }
    }
}
