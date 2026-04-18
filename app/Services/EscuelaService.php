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
    public const HIERARCHICAL_ROLES = ['director', 'vicedirector', 'secretario', 'prosecretario'];

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
     * Valida si el usuario autenticado tiene permisos para asignar un rol específico en una escuela.
     */
    public function validateAssignmentPermissions(int $escuelaId, int $roleId): void
    {
        $admin = auth()->user();
        if (!$admin) {
            throw new \Exception("Usuario no autenticado", 401);
        }

        $isSuperUser = $admin->hasRole('superuser');
        $isJefeDistrital = $admin->hasRole('jefe_distrital');
        $role = \Spatie\Permission\Models\Role::findOrFail($roleId);
        $isTargetHierarchical = in_array($role->name, self::HIERARCHICAL_ROLES);

        if ($isSuperUser) {
            return;
        }

        if (!$isJefeDistrital) {
            // Personal Jerárquico Local
            if ($isTargetHierarchical) {
                 throw new \Exception("No tienes permisos para asignar roles jerárquicos.", 403);
            }

            // Verificar si el admin tiene rol jerárquico en la escuela destino
            $isAdminHierarchicalInSchool = EscuelaUsuario::where('usuario_id', $admin->id)
                ->where('escuela_id', $escuelaId)
                ->whereHas('role', function($q) {
                    $q->whereIn('name', self::HIERARCHICAL_ROLES);
                })
                ->whereNotNull('verified_at')
                ->exists();

            if (!$isAdminHierarchicalInSchool) {
                throw new \Exception("No tienes autoridad (rol jerárquico) en esta institución para realizar asignaciones.", 403);
            }
        } else {
            // Jefe Distrital: Solo puede asignar roles jerárquicos
            if (!$isTargetHierarchical) {
                throw new \Exception("Los puestos operativos solo pueden ser asignados por personal jerárquico de la institución.", 403);
            }
        }
    }

    /**
     * Direct assign a role to a user in a school (verified).
     * This method is now intended for administrative overrides or CUPOF syncing.
     */
    public function assignDirect(Usuario $targetUser, int $escuelaId, int $roleId): EscuelaUsuario
    {
        $this->validateAssignmentPermissions($escuelaId, $roleId);

        $link = EscuelaUsuario::updateOrCreate(
            [
                'usuario_id' => $targetUser->id,
                'escuela_id' => $escuelaId,
                'role_id' => $roleId
            ],
            [
                'verified_at' => now(),
                'updated_by' => auth()->id()
            ]
        );

        if ($targetUser->estado !== 'activo') {
            $targetUser->update(['estado' => 'activo']);
        }

        return $link->load(['usuario.persona', 'escuela', 'role']);
    }
}
