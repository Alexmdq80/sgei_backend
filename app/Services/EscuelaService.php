<?php

namespace App\Services;

use App\Models\Escuela;
use App\Models\Usuario;
use App\Models\EscuelaUsuario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EscuelaService
{
    /**
     * Get schools with search and filters.
     */
    public function search(string $term = null, array $filters = []): Collection
    {
        $query = Escuela::query()->select(['id', 'nombre', 'numero', 'cue_anexo', 'clave_provincial', 'localidad_id', 'sector_id']);

        // Filtro por término de búsqueda
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('numero', 'like', "%{$term}%")
                  ->orWhere('cue_anexo', 'like', "%{$term}%")
                  ->orWhere('clave_provincial', 'like', "%{$term}%");
            });
        }

        // Filtros Geográficos
        if (!empty($filters['localidad_id'])) {
            $query->where('localidad_id', $filters['localidad_id']);
        } elseif (!empty($filters['departamento_id'])) {
            $query->whereHas('localidad', function ($q) use ($filters) {
                $q->where('departamento_id', $filters['departamento_id']);
            });
        } elseif (!empty($filters['provincia_id'])) {
            $query->whereHas('localidad.departamento', function ($q) use ($filters) {
                $q->where('provincia_id', $filters['provincia_id']);
            });
        }

        // Filtro por Nivel
        if (!empty($filters['nivel_id'])) {
            $query->whereHas('modalidadesNiveles', function ($q) use ($filters) {
                $q->where('modalidad_nivel.nivel_id', $filters['nivel_id']);
            });
        }

        // Filtro por Sector
        if (!empty($filters['sector_id'])) {
            $query->where('sector_id', $filters['sector_id']);
        }

        return $query->with(['localidad:id,nombre', 'sector:id,nombre'])->limit(50)->get();
    }

    /**
     * Request to join a school.
     */
    public function requestJoin(Usuario $user, int $escuelaId, ?int $roleId = null): void
    {
        // Si no se provee rol, buscamos el rol 'profesor' como predeterminado (o 'responsable' según lógica de negocio)
        if (!$roleId) {
            $roleId = \Spatie\Permission\Models\Role::where('name', 'profesor')->where('guard_name', 'sanctum')->first()?->id;
        }

        EscuelaUsuario::updateOrCreate(
            [
                'usuario_id' => $user->id,
                'escuela_id' => $escuelaId
            ],
            [
                'role_id' => $roleId,
                'verified_at' => null,
            ]
        );

        $user->update(['estado' => 'espera_aprobacion']);
    }

    /**
     * Get pending school join requests.
     */
    public function getPendingRequests(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $user = auth()->user();
        $isSuperUser = $user->hasRole('superuser');
        $hierarchicalRoles = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        $query = EscuelaUsuario::whereNull('verified_at')
            ->with(['usuario.persona', 'escuela', 'role']);

        if ($isSuperUser) {
            // El Super Admin ve principalmente las solicitudes de roles jerárquicos
            // Pero permitimos ver todo si se filtra por escuela específica
            if (empty($filters['escuela_id'])) {
                $query->whereHas('role', function($q) use ($hierarchicalRoles) {
                    $q->whereIn('name', $hierarchicalRoles);
                });
            }
        } else {
            // El personal jerárquico solo ve solicitudes operativas de sus escuelas vinculadas
            $escuelasIds = $user->escuelaUsuarios()
                ->whereNotNull('verified_at')
                ->pluck('escuela_id');

            $query->whereIn('escuela_id', $escuelasIds)
                ->whereHas('role', function($q) use ($hierarchicalRoles) {
                    $q->whereNotIn('name', $hierarchicalRoles);
                });
        }

        if (!empty($filters['escuela_id'])) {
            $query->where('escuela_id', $filters['escuela_id']);
        }

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->whereHas('usuario', function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Approve a school join request.
     */
    public function approveJoin(string $requestId, ?int $roleId = null): EscuelaUsuario
    {
        $request = EscuelaUsuario::findOrFail($requestId);
        $user = auth()->user();
        $isSuperUser = $user->hasRole('superuser');
        $hierarchicalRoles = ['director', 'vicedirector', 'secretario', 'prosecretario'];
        
        // Determinar el rol final para validar permisos
        $targetRoleId = $roleId ?: $request->role_id;
        $targetRole = \Spatie\Permission\Models\Role::findOrFail($targetRoleId);
        $isTargetHierarchical = in_array($targetRole->name, $hierarchicalRoles);

        // 1. Solo superuser puede aprobar roles jerárquicos
        if ($isTargetHierarchical && !$isSuperUser) {
            throw new \Exception("No tienes permisos para aprobar roles jerárquicos.", 403);
        }

        // 2. Si no es superuser, debe pertenecer a la misma escuela y estar verificado
        if (!$isSuperUser) {
            $isLinked = $user->escuelaUsuarios()
                ->where('escuela_id', $request->escuela_id)
                ->whereNotNull('verified_at')
                ->exists();
            
            if (!$isLinked) {
                throw new \Exception("No perteneces a esta institución para realizar aprobaciones.", 403);
            }

            // Opcional: Verificar que tenga permiso de sistema.usuarios
            if (!$user->hasPermissionTo('sistema.usuarios')) {
                throw new \Exception("No tienes permisos de gestión de usuarios.", 403);
            }
        }
        
        $data = [
            'verified_at' => now(),
            'updated_by' => $user->id
        ];

        // Si el administrador asigna un rol diferente al solicitado
        if ($roleId) {
            $data['role_id'] = $roleId;
        }

        $request->update($data);

        // Actualizar el estado del usuario si era "espera_aprobacion"
        $usuario = $request->usuario;
        if ($usuario->estado === 'espera_aprobacion') {
            $usuario->update(['estado' => 'activo']);
        }

        return $request->load(['usuario.persona', 'escuela', 'role']);
    }

    /**
     * Reject a school join request.
     */
    public function rejectJoin(string $requestId, ?string $reason = null): void
    {
        $request = EscuelaUsuario::findOrFail($requestId);
        $usuario = $request->usuario;

        // Si se proporciona una razón, guardarla en el usuario (opcional, según lógica de negocio)
        if ($reason) {
            $usuario->update(['motivo_rechazo' => $reason]);
        }

        $request->delete();

        // Si no quedan solicitudes, volver al estado inicial
        if (EscuelaUsuario::where('usuario_id', $usuario->id)->count() === 0) {
            $usuario->update(['estado' => 'email_verificado']);
        }
    }

    /**
     * Cancel a school join request.
     */
    public function cancelJoin(Usuario $user, int $escuelaId): void
    {
        $request = EscuelaUsuario::where('usuario_id', $user->id)
            ->where('escuela_id', $escuelaId)
            ->whereNull('verified_at')
            ->firstOrFail();

        $request->delete();

        // Si no quedan solicitudes, volver al estado inicial
        if (EscuelaUsuario::where('usuario_id', $user->id)->count() === 0) {
            $user->update(['estado' => 'email_verificado']);
        }
    }
}
