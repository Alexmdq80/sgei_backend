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
            'sexos' => 'sexo',
            'generos' => 'genero',
            'documento_tipos' => 'documento_tipo',
            'documento_situacions' => 'documento_situacion',
            'vinculo_tipos' => 'vinculo_tipo',
            'vinculos' => 'adulto_vinculo',
            'ambitos' => 'ambito',
            'dependencias' => 'dependencia',
            'sectors' => 'sector',
            'continentes' => 'continente',
            'nacions' => 'pais',
            'provincias' => 'provincia',
            'regions' => 'region_educativa',
            'departamentos' => 'departamento',
            'municipios' => 'municipio',
            'localidad_censals' => 'localidad_censal',
            'localidads' => 'localidad',
            'georef_fuentes' => 'fuente_georef',
            'georef_categorias' => 'categoria_georef',
            'georef_funcions' => 'funcion_georef',
            'georef_localidads' => 'localidad_asentamiento',
            'georef_asentamientos' => 'asentamiento',
            'calles' => 'calle',
            'escuela_tipos' => 'tipo_escuela',
            'nivels' => 'nivel',
            'modalidads' => 'modalidad',
            'jornadas' => 'jornada',
            'turnos' => 'turno',
            'ofertas' => 'otras_ofertas',
            'lectivos' => 'ciclo_lectivo',
            'anios' => 'anio',
            'plan_ciclos' => 'ciclo_plan_estudio',
            'plans' => 'plan_estudio',
            'anio_plan' => 'plan_anios',
            'escuelas' => 'escuela',
            'personas' => 'persona',
            'usuarios' => 'usuario',
            'escuela_usuario' => 'usuario_escuela',
            'domicilios' => 'domicilio',
            'contactos' => 'contacto',
            'persona_vinculo_persona' => 'estudiante_adulto_vinculo',
            'salida_motivos' => 'salida_motivo',
            'cierre_causas' => 'inscripcion_cierre',
            'condicions' => 'condicion',
            'seccion_tipos' => 'seccion_tipo',
            'escuela_ubicacions' => 'ubicacion_escuela',
            'espacios' => 'espacio_academico',
            'legajos' => 'legajo',
            'propuestas' => 'propuesta_institucional',
            'escuela_modalidad_nivel' => 'escuela_nivel_modalidad',
            'escuela_oferta' => 'escuela_otras_ofertas',
            'inscripcions' => 'inscripcion',
            'historial_inscripcions' => 'inscripcion_historial',
            'historial_info_inscripcions' => 'inscripcion_historial_info',
            'inscripcion_pases' => 'inscripcion_pase',
            'inscripcion_bajas' => 'inscripcion_baja',
            'inscripcion_finalizados' => 'inscripcion_finalizado',
        ];

        // Mapeo de nombres de columnas (Destino => Origen Legacy)
        $columnMappings = [
            'vinculos' => [
                'vinculo_tipo_id' => 'id_vinculo_tipo',
            ],
            'nacions' => [
                'continente_id' => 'id_continente',
            ],
            'provincias' => [
                'nacion_id' => 'id_pais',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'regions' => [
                'numero' => 'id',
            ],
            'departamentos' => [
                'provincia_id' => 'id_provincia',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'municipios' => [
                'provincia_id' => 'id_provincia',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'localidad_censals' => [
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
                'georef_funcion_id' => 'id_funcion_georef',
            ],
            'localidads' => [
                'departamento_id' => 'id_departamento',
                'municipio_id' => 'id_municipio',
                'localidad_censal_id' => 'id_localidad_censal',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'georef_localidads' => [
                'departamento_id' => 'id_departamento',
                'municipio_id' => 'id_municipio',
                'localidad_censal_id' => 'id_localidad_censal',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'georef_asentamientos' => [
                'departamento_id' => 'id_departamento',
                'municipio_id' => 'id_municipio',
                'localidad_censal_id' => 'id_localidad_censal',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'calles' => [
                'localidad_censal_id' => 'id_localidad_censal',
                'georef_fuente_id' => 'id_fuente_georef',
                'georef_categoria_id' => 'id_categoria_georef',
            ],
            'escuelas' => [
                'ambito_id' => 'id_ambito',
                'dependencia_id' => 'id_dependencia',
                'sector_id' => 'id_sector',
            ],
            'personas' => [
                'documento_tipo_id' => 'id_documento_tipo',
                'documento_situacion_id' => 'id_documento_situacion',
                'sexo_id' => 'id_sexo',
                'genero_id' => 'id_genero',
            ],
            'escuela_usuario' => [
                'escuela_id' => 'id_escuela',
                'usuario_id' => 'id_usuario',
                'role_id' => 'usuario_tipo_id',
            ],
            'domicilios' => [
                'persona_id' => 'id_persona',
                'calle_id' => 'id_calle',
            ],
            'contactos' => [
                'persona_id' => 'id_persona',
            ],
            'persona_vinculo_persona' => [
                'persona_estudiante_id' => 'id_persona_estudiante',
                'persona_adulto_id' => 'id_persona_adulto',
            ],
            'espacios' => [
                'seccion_tipo_id' => 'id_seccion_tipo',
            ],
            'legajos' => [
                'persona_id' => 'id_persona',
                'escuela_id' => 'id_escuela',
            ],
            'propuestas' => [
                'anio_plan_id' => 'id_anio_plan',
                'turno_inicio_id' => 'id_turno_inicio',
                'turno_fin_id' => 'id_turno_fin',
                'jornada_id' => 'id_jornada',
            ],
            'escuela_modalidad_nivel' => [
                'escuela_id' => 'id_escuela',
            ],
            'escuela_oferta' => [
                'escuela_id' => 'id_escuela',
            ],
            'inscripcions' => [
                'persona_id' => 'id_persona',
                'persona_firma_id' => 'id_persona_firma',
                'condicion_id' => 'id_condicion',
            ],
            'historial_inscripcions' => [
                'persona_id' => 'id_persona',
                'persona_firma_id' => 'id_persona_firma',
                'condicion_id' => 'id_condicion',
            ],
            'historial_info_inscripcions' => [
                'historial_inscripcion_id' => 'id_inscripcion_historial',
            ],
            'inscripcion_pases' => [
                'escuela_id' => 'id_escuela',
                'historial_inscripcion_id' => 'id_inscripcion_historial',
                'salida_motivo_id' => 'id_salida_motivo',
                'escuela_ubicacion_id' => 'id_ubicacion_escuela',
            ],
            'inscripcion_bajas' => [
                'historial_inscripcion_id' => 'id_inscripcion_historial',
                'salida_motivo_id' => 'id_salida_motivo',
            ],
            'inscripcion_finalizados' => [
                'historial_inscripcion_id' => 'id_inscripcion_historial',
                'condicion_id' => 'id_condicion',
            ],
        ];

        $legacyTableName = $tableMappings[$tableName] ?? $tableName;

        try {
            if (!Schema::connection('legacy')->hasTable($legacyTableName)) {
                $this->warn("   - Saltando: La tabla '{$legacyTableName}' no existe en la base de datos legacy.");
                return;
            }

            $destinationColumns = Schema::getColumnListing($tableName);
            
            // Verificamos si la tabla de origen tiene registros
            $hasData = DB::connection('legacy')->table($legacyTableName)->exists();

            if (!$hasData) {
                $this->line("   - Tabla '{$legacyTableName}' vacía.");
                return;
            }

            // Limpiar tabla destino antes de insertar
            DB::table($tableName)->truncate();

            // Cargar mapeo de roles solo una vez cuando se migre escuela_usuario
            if ($tableName === 'escuela_usuario') {
                $legacyRoles = DB::connection('legacy')->table('usuario_tipo')->get();
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

            // Convertir y filtrar datos en bloques de forma eficiente usando lazy() para no saturar memoria
            $dataToInsert = [];
            $totalMigrated = 0;

            foreach (DB::connection('legacy')->table($legacyTableName)->orderBy('id')->lazy(1000) as $item) {
                $legacyArray = (array) $item;
                $filteredArray = [];

                foreach ($destinationColumns as $column) {
                    $legacyColumnName = $columnMappings[$tableName][$column] ?? $column;

                    if (array_key_exists($legacyColumnName, $legacyArray)) {
                        $filteredArray[$column] = $legacyArray[$legacyColumnName];

                        // Lógica Especial: Mapeo de Roles
                        if ($tableName === 'escuela_usuario' && $column === 'role_id') {
                            $filteredArray[$column] = $this->roleMap[$filteredArray[$column]] ?? null;
                            if (!$filteredArray[$column]) {
                                continue 2; // continuar con la siguiente fila
                            }
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

                $dataToInsert[] = $filteredArray;

                if (count($dataToInsert) >= 1000) {
                    DB::table($tableName)->insert($dataToInsert);
                    $totalMigrated += count($dataToInsert);
                    $dataToInsert = [];
                }
            }

            if (!empty($dataToInsert)) {
                DB::table($tableName)->insert($dataToInsert);
                $totalMigrated += count($dataToInsert);
            }

            if ($tableName === 'escuelas') {
                $this->unknownEscuelaId = DB::table('escuelas')->insertGetId(['nombre' => 'DESCONOCIDO / SIN DATOS', 'created_at' => now(), 'updated_at' => now()]);
            }

            $this->info("✓ Se migraron {$totalMigrated} registros en '{$tableName}'.");

        } catch (\Exception $e) {
            $this->error("Error en '{$tableName}': " . $e->getMessage());
        }
    }
}
