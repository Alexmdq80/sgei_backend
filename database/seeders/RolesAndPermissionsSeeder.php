<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- DEFINICIÓN DE PERMISOS ATÓMICOS ---

        $permissions = [
            // Gestión Institucional
            'institucion.ver', 'institucion.editar', 'institucion.configurar', 'institucion.ciclos',
            
            // Gestión de Estudiantes (ex-alumnos)
            'estudiantes.ver', 'estudiantes.ver.sensible', 'estudiantes.crear', 'estudiantes.editar', 
            'estudiantes.inscribir', 'estudiantes.pases', 'estudiantes.bajas',
            
            // Gestión de Personal
            'personal.ver', 'personal.gestionar', 'personal.asignar',
            
            // Gestión Académica
            'notas.ver', 'notas.cargar', 'notas.cerrar',
            'asistencia.ver', 'asistencia.cargar', 'asistencia.justificar',
            'boletines.generar',
            
            // Familia y Vínculos
            'familia.gestionar', 'familia.notificar',
            
            // Sistema y Seguridad
            'sistema.usuarios', 'sistema.roles', 'sistema.auditoria',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // --- DEFINICIÓN DE ROLES ---

        // 1. Super Administrador (Acceso Total)
        $superUser = Role::firstOrCreate(['name' => 'superuser', 'guard_name' => 'sanctum']);
        $superUser->givePermissionTo(Permission::all());

        // 2. Roles Directivos (Director / Vicedirector)
        $director = Role::firstOrCreate(['name' => 'director', 'guard_name' => 'sanctum']);
        $vicedirector = Role::firstOrCreate(['name' => 'vicedirector', 'guard_name' => 'sanctum']);
        
        $directivoPermissions = [
            'institucion.ver', 'institucion.editar', 'institucion.configurar', 'institucion.ciclos',
            'estudiantes.ver', 'estudiantes.ver.sensible', 'estudiantes.crear', 'estudiantes.editar', 'estudiantes.inscribir', 'estudiantes.pases', 'estudiantes.bajas',
            'personal.ver', 'personal.gestionar', 'personal.asignar',
            'notas.ver', 'notas.cargar', 'notas.cerrar',
            'asistencia.ver', 'asistencia.cargar', 'asistencia.justificar',
            'boletines.generar', 'familia.gestionar', 'familia.notificar',
            'sistema.usuarios' // Solo gestión de usuarios locales
        ];
        $director->givePermissionTo($directivoPermissions);
        $vicedirector->givePermissionTo($directivoPermissions);

        // 3. Roles Secretaría (Secretario / Prosecretario)
        $secretario = Role::firstOrCreate(['name' => 'secretario', 'guard_name' => 'sanctum']);
        $prosecretario = Role::firstOrCreate(['name' => 'prosecretario', 'guard_name' => 'sanctum']);
        
        $secretariaPermissions = [
            'institucion.ver', 'institucion.ciclos',
            'estudiantes.ver', 'estudiantes.crear', 'estudiantes.editar', 'estudiantes.inscribir', 'estudiantes.pases', 'estudiantes.bajas',
            'personal.ver', 'asistencia.ver', 'boletines.generar', 'familia.gestionar',
            'sistema.usuarios' // Habilitado para gestión delegada
        ];
        $secretario->givePermissionTo($secretariaPermissions);
        $prosecretario->givePermissionTo($secretariaPermissions);

        // 4. Preceptor
        $preceptor = Role::firstOrCreate(['name' => 'preceptor', 'guard_name' => 'sanctum']);
        $preceptor->givePermissionTo([
            'institucion.ver', 'estudiantes.ver', 'asistencia.ver', 'asistencia.cargar', 
            'asistencia.justificar', 'notas.ver', 'familia.notificar'
        ]);

        // 5. Profesor
        $profesor = Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'sanctum']);
        $profesor->givePermissionTo([
            'institucion.ver', 'estudiantes.ver', 'notas.ver', 'notas.cargar', 
            'asistencia.cargar', 'boletines.generar'
        ]);

        // 6. Estudiante
        $estudiante = Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'sanctum']);
        $estudiante->givePermissionTo(['institucion.ver', 'estudiantes.ver', 'notas.ver', 'asistencia.ver']);

        // 7. Responsable (Padre/Madre/Tutor)
        $responsable = Role::firstOrCreate(['name' => 'responsable', 'guard_name' => 'sanctum']);
        $responsable->givePermissionTo(['institucion.ver', 'estudiantes.ver', 'notas.ver', 'asistencia.ver']);

        // 8. Jefe Distrital (Regional Admin)
        $jefeDistrital = Role::firstOrCreate(['name' => 'jefe_distrital', 'guard_name' => 'sanctum']);
        $jefeDistrital->givePermissionTo([
            'institucion.ver',
            'personal.ver',
            'planes.ver',
            'sistema.usuarios',
            'sistema.roles'
        ]);

        // 9. Supervisor Curricular (Academic Focus)
        $supervisor = Role::firstOrCreate(['name' => 'supervisor_curricular', 'guard_name' => 'sanctum']);
        $supervisor->givePermissionTo([
            'institucion.ver',
            'personal.ver',
            'planes.ver',
            'planes.crear',
            'planes.editar',
            'planes.eliminar',
            'asignaturas.ver',
            'asignaturas.gestionar',
            'sistema.usuarios' // Solo lectura (protegido por controlador)
        ]);
    }
}
