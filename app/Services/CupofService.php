<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Cupof;
use App\Models\CupofMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CupofService
{
    /**
     * Get all CUPOFs with their current occupant and relations.
     */
    public function getAllCupofs(array $filters = []): Collection
    {
        $user = auth()->user();
        if (!$user) return collect();

        $isSuperUser = $user->hasRole('superuser');
        $isJefeDistrital = $user->hasRole('jefe_distrital');
        $query = Cupof::with(['escuela', 'asignatura', 'escalafon', 'puestoTipo', 'movimientoActivo.persona']);

        // Bypass for Superusers: see everything
        if ($isSuperUser) {
            // No filter applied here, proceeds to global filters
        }
        // 1. Restriction for Jefe Distrital: Only see hierarchical positions
        elseif ($isJefeDistrital) {
            $hierarchicalKeywords = \App\Services\EscuelaService::HIERARCHICAL_ROLES;
            $query->where(function ($q) use ($hierarchicalKeywords) {
                foreach ($hierarchicalKeywords as $keyword) {
                    $q->orWhere('nombre_cargo', 'like', "%{$keyword}%");
                }
            });
        } 
        // 2. Restriction for Conduction Team: Only see their own school(s)
        else {
            $schoolIds = $user->escuelaUsuarios()
                ->whereHas('role', function($q) {
                    $q->whereIn('name', \App\Services\EscuelaService::HIERARCHICAL_ROLES);
                })
                ->whereNotNull('verified_at')
                ->pluck('escuela_id');
            
            $query->whereIn('escuela_id', $schoolIds);
        }

        if (isset($filters['escuela_id']) && !empty($filters['escuela_id'])) {
            $query->where('escuela_id', $filters['escuela_id']);
        }

        if (isset($filters['estado_cupof']) && !empty($filters['estado_cupof'])) {
            $query->where('estado_cupof', $filters['estado_cupof']);
        }

        if (isset($filters['escalafon_id']) && !empty($filters['escalafon_id'])) {
            $query->where('escalafon_id', $filters['escalafon_id']);
        }

        return $query->get();
    }

    /**
     * Create a new CUPOF slot.
     */
    public function createCupof(array $data): Cupof
    {
        $this->validateHierarchicalAccess($data['nombre_cargo'] ?? '');

        return Cupof::create([
            'codigo_cupof' => $data['codigo_cupof'],
            'escuela_id' => $data['escuela_id'],
            'asignatura_id' => $data['asignatura_id'] ?? null,
            'nombre_cargo' => $data['nombre_cargo'] ?? null,
            'escalafon_id' => $data['escalafon_id'],
            'puesto_tipo_id' => $data['puesto_tipo_id'],
            'cantidad' => $data['cantidad'] ?? 1,
            'estado_cupof' => 'disponible',
        ]);
    }

    /**
     * Assign a persona to a CUPOF slot.
     */
    public function assignPersona(Cupof $cupof, Persona $persona, array $details): CupofMovimiento
    {
        $this->validateHierarchicalAccess($cupof->nombre_cargo ?? '');

        return DB::transaction(function () use ($cupof, $persona, $details) {
            // 1. Deactivate any current active movement just in case
            $cupof->movimientos()->where('activo', true)->update(['activo' => false, 'fecha_fin' => now()]);

            // 2. Create the new movement
            $movimiento = CupofMovimiento::create([
                'cupof_id' => $cupof->id,
                'persona_id' => $persona->id,
                'situacion_revista' => $details['situacion_revista'],
                'fecha_inicio' => $details['fecha_inicio'] ?? now(),
                'resolucion' => $details['resolucion'] ?? null,
                'activo' => true,
            ]);

            // 3. Update CUPOF status
            $cupof->update(['estado_cupof' => 'ocupado']);

            // 4. Sync School-User Link and Role
            $this->syncEscuelaUsuario($cupof, $persona);

            return $movimiento;
        });
    }

    /**
     * Release a CUPOF slot (e.g. resignation or section closure).
     */
    public function releaseCupof(Cupof $cupof, ?string $motivoBaja = null): bool
    {
        $this->validateHierarchicalAccess($cupof->nombre_cargo ?? '');

        $persona = $cupof->movimientoActivo?->persona;

        return DB::transaction(function () use ($cupof, $motivoBaja, $persona) {
            // 1. Deactivate current occupant
            $cupof->movimientos()->where('activo', true)->update([
                'activo' => false, 
                'fecha_fin' => now()
            ]);

            // 2. Update CUPOF status
            $status = $motivoBaja ? 'baja' : 'disponible';
            $updated = $cupof->update([
                'estado_cupof' => $status,
                'motivo_baja' => $motivoBaja
            ]);

            // 3. Sync/Revoke School-User Link if persona exists
            if ($persona) {
                $this->syncEscuelaUsuario($cupof, $persona, true);
            }

            return $updated;
        });
    }

    /**
     * Validates if the current user has permissions to manage the given cargo name.
     */
    private function validateHierarchicalAccess(string $nombreCargo): void
    {
        $user = auth()->user();
        if (!$user) throw new \Exception("Usuario no autenticado", 401);

        // Bypass for Superusers: Total access
        if ($user->hasRole('superuser')) return;

        $isJefeDistrital = $user->hasRole('jefe_distrital');
        
        $isHierarchical = false;
        $nombreCargo = mb_strtolower($nombreCargo, 'UTF-8');
        foreach (\App\Services\EscuelaService::HIERARCHICAL_ROLES as $role) {
            if (str_contains($nombreCargo, $role)) {
                $isHierarchical = true;
                break;
            }
        }

        if ($isJefeDistrital) {
            if (!$isHierarchical) {
                throw new \Exception("Como Jefe Distrital, solo tienes permitido gestionar cargos del Equipo de Conducción.", 403);
            }
        } else {
            // Equipo de Conducción (Director, Vice, etc.)
            // Now allowed to manage all positions in their school (including hierarchical)
            // No restriction here, just verify they are indeed conduction (this is double-checked by the Policy)
        }
    }

    /**
     * Syncs the EscuelaUsuario record based on CUPOF assignments.
     */
    private function syncEscuelaUsuario(Cupof $cupof, Persona $persona, bool $isRelease = false): void
    {
        $usuario = $persona->usuario;
        if (!$usuario) return;

        $escuelaId = $cupof->escuela_id;

        // If it's a release, check if the persona still has other active CUPOFs in the same school
        if ($isRelease) {
            $hasOtherCupofs = CupofMovimiento::where('persona_id', $persona->id)
                ->where('activo', true)
                ->whereHas('cupof', function($q) use ($escuelaId) {
                    $q->where('escuela_id', $escuelaId);
                })
                ->exists();

            if (!$hasOtherCupofs) {
                // If no more CUPOFs, we mark the link as inactive (Soft Delete or verified_at null)
                \App\Models\EscuelaUsuario::where('usuario_id', $usuario->id)
                    ->where('escuela_id', $escuelaId)
                    ->update(['verified_at' => null]);
            } else {
                // Recalculate highest role if still has positions
                $this->refreshUserRoleInSchool($usuario, $escuelaId, $persona);
            }
            return;
        }

        // If it's an assignment, ensure the link exists and is active
        $this->refreshUserRoleInSchool($usuario, $escuelaId, $persona);
    }

    /**
     * Determines and syncs all roles the user has in a school based on all active CUPOFs.
     */
    private function refreshUserRoleInSchool($usuario, $escuelaId, $persona): void
    {
        // 1. Get all unique roles derived from active CUPOFs for this persona in this school
        $activeCupofs = Cupof::with(['escalafon', 'puestoTipo'])->whereHas('movimientos', function($q) use ($persona) {
            $q->where('persona_id', $persona->id)->where('activo', true);
        })->where('escuela_id', $escuelaId)->get();

        if ($activeCupofs->isEmpty()) return;

        $uniqueRolesInSchool = $activeCupofs->map(fn($c) => $this->mapCupofToRole($c))->unique();

        // 2. Map role names to Role IDs
        $roleIds = \Spatie\Permission\Models\Role::whereIn('name', $uniqueRolesInSchool)
            ->where('guard_name', 'sanctum')
            ->pluck('id', 'name');

        // 3. For each unique role, ensure a verified link exists in escuela_usuario
        foreach ($uniqueRolesInSchool as $roleName) {
            $roleId = $roleIds[$roleName] ?? null;
            if (!$roleId) continue;

            \App\Models\EscuelaUsuario::updateOrCreate(
                [
                    'usuario_id' => $usuario->id, 
                    'escuela_id' => $escuelaId,
                    'role_id' => $roleId
                ],
                [
                    'verified_at' => now(), // Auto-verify administrative assignments
                ]
            );
        }

        // 4. Cleanup: Remove roles that the user NO LONGER has in this school via CUPOF
        $rolesToKeep = $roleIds->values()->toArray();
        \App\Models\EscuelaUsuario::where('usuario_id', $usuario->id)
            ->where('escuela_id', $escuelaId)
            ->whereNotNull('role_id')
            ->whereNotIn('role_id', $rolesToKeep)
            ->delete();

        // Ensure user status is active
        if ($usuario->estado !== 'activo') {
            $usuario->update(['estado' => 'activo']);
        }
    }

    /**
     * Maps a single CUPOF to a Role name.
     */
    private function mapCupofToRole(Cupof $cupof): string
    {
        // Prioritize the specific cargo name if it exists
        $cargo = mb_strtolower($cupof->nombre_cargo ?? '', 'UTF-8');
        $tipoPuesto = mb_strtolower($cupof->puestoTipo?->nombre ?? '', 'UTF-8');
        $escalafon = mb_strtolower($cupof->escalafon?->nombre ?? '', 'UTF-8');
        
        $searchString = $cargo . ' ' . $tipoPuesto . ' ' . $escalafon;
        
        // Check hierarchical titles
        if (str_contains($searchString, 'director')) return 'director';
        if (str_contains($searchString, 'vice')) return 'vicedirector';
        if (str_contains($searchString, 'secretario')) return 'secretario';
        if (str_contains($searchString, 'prosecretario')) return 'prosecretario';
        if (str_contains($searchString, 'preceptor')) return 'preceptor';

        // Fallback to escalafon
        if (str_contains($escalafon, 'docente')) return 'profesor';
        if (str_contains($escalafon, 'auxiliar')) return 'auxiliar';

        return 'profesor';
    }
}
