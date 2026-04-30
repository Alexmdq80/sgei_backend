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

        // Verificación de Roles (Crítico para escuela_usuario)
        $spatieRoles = \Spatie\Permission\Models\Role::all();
        if ($spatieRoles->count() <= 2) { // 'superuser' y 'supervisor_curricular' suelen ser los únicos por defecto
            $this->warn('⚠️ La tabla de roles parece estar incompleta. Se recomienda ejecutar:');
            $this->warn('   php artisan db:seed --class=RolesAndPermissionsSeeder');
            if (!$this->confirm('¿Desea continuar de todos modos?', false)) {
                return;
            }
        }

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
            'modalidad_nivel',
            'jornadas',
            'turnos',
            'ofertas',
            'lectivos',
            'anios',
            'plan_ciclos',
            'plans',
            'anio_plan',
            'escuelas',
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
            // Tablas nuevas que podrían tener datos en legacy (si se añadieron allí)
            'cargos',
            'agentes',
            'cupofs',
            'cupof_movimientos',
            'asignaturas',
        ];

        foreach ($tablesToMigrate as $tableName) {
            $this->migrateTable($tableName);
        }

        // Reactivar restricciones de claves foráneas
        Schema::enableForeignKeyConstraints();

        $this->info('--- MIGRACIÓN DE LEGACY COMPLETADA ---');
        $this->info('Recuerde ejecutar los seeders adicionales si es necesario:');
        $this->line(' - php artisan db:seed --class=AsignaturaSeeder');
        $this->line(' - php artisan db:seed --class=CargoSeeder');
    }

    /**
     * Mapeo de roles de legacy a Spatie (Cache local para el comando).
     */
    private array $roleMap = [];

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
            'anio_plan' => 'plan_anios',
        ];

        // Mapeo de nombres de columnas (Destino => Origen Legacy)
        $columnMappings = [
            'escuela_usuario' => [
                'role_id' => 'usuario_tipo_id'
            ],
            'propuestas' => [
                'anio_plan_id' => 'plan_anio_id'
            ]
        ];

        $legacyTableName = $tableMappings[$tableName] ?? $tableName;

        try {
            if (!Schema::connection('legacy')->hasTable($legacyTableName)) {
                $this->warn("   - Saltando: La tabla '{$legacyTableName}' no existe en la base de datos legacy.");
                return;
            }

            $destinationColumns = Schema::getColumnListing($tableName);
            $legacyData = DB::connection('legacy')->table($legacyTableName)->get();

            if ($legacyData->isEmpty()) {
                $this->line("   - Tabla '{$legacyTableName}' vacía.");
                return;
            }

            // Limpiar tabla destino antes de insertar
            DB::table($tableName)->truncate();

            // Cargar mapeo de roles solo una vez cuando se migre escuela_usuario
            if ($tableName === 'escuela_usuario') {
                $legacyRoles = DB::connection('legacy')->table('usuario_tipos')->get();
                $spatieRoles = \Spatie\Permission\Models\Role::all();

                foreach ($legacyRoles as $lr) {
                    $nameMatch = strtolower($lr->nombre);
                    if ($nameMatch === 'administrador') $nameMatch = 'director';
                    if ($nameMatch === 'personal') $nameMatch = 'profesor';

                    $role = $spatieRoles->firstWhere('name', $nameMatch);
                    if ($role) {
                        $this->roleMap[$lr->id] = $role->id;
                    }
                }
            }

            // Convertir y filtrar datos
            $dataToInsert = $legacyData->map(function ($item) use ($tableName, $destinationColumns, $columnMappings) {
                $legacyArray = (array) $item;
                $filteredArray = [];

                foreach ($destinationColumns as $column) {
                    $legacyColumnName = $columnMappings[$tableName][$column] ?? $column;

                    if (array_key_exists($legacyColumnName, $legacyArray)) {
                        $filteredArray[$column] = $legacyArray[$legacyColumnName];

                        // Lógica Especial: Mapeo de Roles
                        if ($tableName === 'escuela_usuario' && $column === 'role_id') {
                            $filteredArray[$column] = $this->roleMap[$filteredArray[$column]] ?? null;
                            if (!$filteredArray[$column]) return null;
                        }

                        // Manejo de booleanos
                        $booleanColumns = ['asistente_externo_si', 'proyecto_inclusion_si', 'concurre_especial_si'];
                        if (in_array($column, $booleanColumns) && is_null($filteredArray[$column])) {
                            $filteredArray[$column] = 0;
                        }
                    }
                }

                // Fallback de Escuela en inscripcion_pases
                if ($tableName === 'inscripcion_pases' && empty($filteredArray['escuela_id'])) {
                    if (is_null($this->unknownEscuelaId)) {
                        $this->unknownEscuelaId = DB::table('escuelas')->where('nombre', 'DESCONOCIDO / SIN DATOS')->value('id') 
                            ?? DB::table('escuelas')->insertGetId(['nombre' => 'DESCONOCIDO / SIN DATOS', 'created_at' => now(), 'updated_at' => now()]);
                    }
                    $filteredArray['escuela_id'] = $this->unknownEscuelaId;
                }

                return $filteredArray;
            })->filter()->toArray();

            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                DB::table($tableName)->insert($chunk);
            }

            if ($tableName === 'escuelas') {
                $this->unknownEscuelaId = DB::table('escuelas')->insertGetId(['nombre' => 'DESCONOCIDO / SIN DATOS', 'created_at' => now(), 'updated_at' => now()]);
            }

            $count = count($dataToInsert);
            $this->info("✓ Se migraron {$count} registros en '{$tableName}'.");

        } catch (\Exception $e) {
            $this->error("Error en '{$tableName}': " . $e->getMessage());
        }
    }
}
