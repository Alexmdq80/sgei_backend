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
     * ID de la escuela "DESCONOCIDO" para usar como fallback.
     */
    private ?int $unknownEscuelaId = null;

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
            'sexos',
            'generos',
            'documento_tipos',
            'documento_situacions',
            'vinculo_tipos',
            'vinculos',
            'ambitos',
            'dependencias',
            'sectors',
            'continentes',
            'nacions',
            'provincias',
            'regions',
            'departamentos',
            'municipios',
            'localidad_censals',
            'localidads',
            'georef_fuentes',
            'georef_categorias',
            'georef_funcions',
            'georef_localidads',
            'georef_asentamientos',
            'calles',
            'escuela_tipos',
            'nivels',
            'modalidads',
            'modalidad_nivels',
            'jornadas',
            'turnos',
            'ofertas',
            'lectivos',
            'anios',
            'plan_ciclos',
            'plans',
            'anio_plan',
            'escuelas',
            'roles_escolares',
            'personas',
            'usuarios',
            'escuela_usuario',
            'domicilios',
            'contactos',
            'persona_vinculo_persona',
            'salida_motivos',
            'cierre_causas',
            'condicions',
            'seccion_tipos',
            'escuela_ubicacions',
            'espacios',
            'legajos',
            'propuestas',
            'escuela_modalidad_nivel',
            'escuela_oferta',
            'inscripcions',
            'historial_inscripcions',
            'historial_info_inscripcions',
            'inscripcion_pases',
            'inscripcion_bajas',
            'inscripcion_finalizados',
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
     * @param string $tableName El nombre de la tabla en la base de datos NUEVA.
     */
    private function migrateTable(string $tableName): void
    {
        $this->comment("Migrando tabla: {$tableName}...");

        // Mapeo de nombres de tablas (Destino => Origen Legacy)
        $tableMappings = [
            'roles_escolares' => 'usuario_tipos',
            'modalidad_nivels' => 'modalidad_nivel',
            'anio_plan' => 'plan_anios',
        ];

        // Mapeo de nombres de columnas (Destino => Origen Legacy)
        $columnMappings = [
            'escuela_usuario' => [
                'rol_escolar_id' => 'usuario_tipo_id'
            ],
            'propuestas' => [
                'anio_plan_id' => 'plan_anio_id'
            ]
        ];

        $legacyTableName = $tableMappings[$tableName] ?? $tableName;

        try {
            // Verificar si la tabla existe en legacy
            if (!Schema::connection('legacy')->hasTable($legacyTableName)) {
                $this->warn("La tabla '{$legacyTableName}' no existe en la base de datos legacy.");
                return;
            }

            // Obtener columnas de la tabla DESTINO
            $destinationColumns = Schema::getColumnListing($tableName);

            // Obtener datos de la base legacy
            $legacyData = DB::connection('legacy')->table($legacyTableName)->get();

            if ($legacyData->isEmpty()) {
                $this->line("La tabla '{$legacyTableName}' está vacía.");
                return;
            }

            // Limpiar tabla destino antes de insertar
            DB::table($tableName)->truncate();

            // Convertir y filtrar datos
            $dataToInsert = $legacyData->map(function ($item) use ($tableName, $destinationColumns, $columnMappings) {
                $legacyArray = (array) $item;
                $filteredArray = [];

                foreach ($destinationColumns as $column) {
                    // Si hay un mapeo de columna para esta tabla y columna destino
                    $legacyColumnName = $columnMappings[$tableName][$column] ?? $column;

                    if (array_key_exists($legacyColumnName, $legacyArray)) {
                        $value = $legacyArray[$legacyColumnName];

                        // Manejo de booleanos obligatorios que vienen nulos
                        $booleanColumns = ['asistente_externo_si', 'proyecto_inclusion_si', 'concurre_especial_si'];
                        if (in_array($column, $booleanColumns) && is_null($value)) {
                            $value = 0;
                        }

                        $filteredArray[$column] = $value;
                    }
                }

                // Caso especial para inscripcion_pases: si no tiene escuela_id, asignar la escuela "DESCONOCIDO"
                if ($tableName === 'inscripcion_pases' && empty($filteredArray['escuela_id'])) {
                    if (is_null($this->unknownEscuelaId)) {
                        $this->unknownEscuelaId = DB::table('escuelas')
                            ->where('nombre', 'DESCONOCIDO / SIN DATOS')
                            ->value('id') ?? DB::table('escuelas')->insertGetId([
                                'nombre' => 'DESCONOCIDO / SIN DATOS',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                    }
                    $filteredArray['escuela_id'] = $this->unknownEscuelaId;
                }

                return $filteredArray;
            })->filter()->toArray(); // filter() elimina los nulos

            // Insertar en bloques para evitar problemas de memoria
            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                DB::table($tableName)->insert($chunk);
            }

            // Al terminar de migrar 'escuelas', creamos la escuela DESCONOCIDO y guardamos su ID
            if ($tableName === 'escuelas') {
                $this->unknownEscuelaId = DB::table('escuelas')->insertGetId([
                    'nombre' => 'DESCONOCIDO / SIN DATOS',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->info("   - Se creó la escuela 'DESCONOCIDO / SIN DATOS' (ID: {$this->unknownEscuelaId}).");
            }

            $count = count($dataToInsert);
            $this->info("✓ Se migraron {$count} registros en '{$tableName}' (desde '{$legacyTableName}').");

        } catch (\Exception $e) {
            $this->error("Error al migrar la tabla '{$tableName}': " . $e->getMessage());
        }
    }
}
