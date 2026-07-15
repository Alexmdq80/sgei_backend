<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockPanelGeneralAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // Si es superuser, siempre tiene acceso
        if ($user->hasRole('superuser') || $user->es_administrador) {
            return $next($request);
        }

        // Listar los roles prohibidos de acceder al Panel General
        $restrictedRoles = [
            'jefe_provincial',
            'jefe_regional',
            'jefe_distrital',
            'director',
            'vicedirector',
            'secretario',
            'prosecretario'
        ];

        // Verificar si el usuario tiene alguno de los roles restringidos
        $hasRestrictedRole = $user->hasAnyRole($restrictedRoles);
        
        if (!$hasRestrictedRole) {
            // Verificar conducción institucional en escuela_usuarios
            $hasRestrictedRole = $user->escuelaUsuarios()
                ->whereNotNull('verified_at')
                ->whereHas('role', function($q) use ($restrictedRoles) {
                    $q->whereIn('name', $restrictedRoles);
                })
                ->exists();
        }

        if ($hasRestrictedRole) {
            $path = $request->path();
            
            // Jefe Regional y Jefe Provincial necesitan leer departamentos
            // para poder asignar Jefes Distritales. Solo se permite GET.
            $canReadDepartamentos = $user->hasAnyRole(['jefe_regional', 'jefe_provincial', 'superuser'])
                && preg_match("#api/v1/admin/departamentos(\$|/)#", $path)
                && $request->isMethod('GET');

            if ($canReadDepartamentos) {
                return $next($request);
            }

            // Lista de segmentos de ruta correspondientes al Panel General que deben bloquearse por completo.
            $panelGeneralResources = [
                'cargos',
                'lectivos',
                'ambitos',
                'cierre-causas',
                'condiciones',
                'vinculo-tipos',
                'vinculos',
                'dependencias',
                'escuela-tipos',
                'niveles',
                'modalidades',
                'jornadas',
                'turnos',
                'escalafones',
                'puesto-tipos',
                'escuela-ubicaciones',
                'modalidad-niveles',
                'ofertas',
                'documento-situacions',
                'documento-tipos',
                'generos',
                'sexos',
                'continentes',
                'naciones',
                'provincias',
                'regiones',
                'departamentos',
                'municipios',
                'localidades',
                'localidad-censals',
                'calles',
                'georef-fuentes',
                'georef-categorias',
                'georef-funcions'
            ];

            foreach ($panelGeneralResources as $resource) {
                // Bloquea cualquier ruta como: api/v1/admin/ambitos o api/v1/admin/ambitos/123
                if (preg_match("#api/v1/admin/{$resource}(\$|/)#", $path)) {
                    return response()->json([
                        'error' => 'No tienes permisos para acceder al Panel General.',
                        'code' => 403
                    ], 403);
                }
            }

            // Para 'anios' (Años), que es parte del Panel Curricular:
            // "sólo lectura Panel Curricular": se permite GET, pero se bloquean POST, PUT, PATCH, DELETE.
            if (preg_match("#api/v1/admin/anios(\$|/)#", $path)) {
                if (!$request->isMethod('GET')) {
                    return response()->json([
                        'error' => 'No tienes permisos para modificar elementos del Panel Curricular.',
                        'code' => 403
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
