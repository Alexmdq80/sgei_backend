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
        $query = Cupof::with(['escuela', 'asignatura', 'movimientoActivo.persona']);

        if (isset($filters['escuela_id'])) {
            $query->where('escuela_id', $filters['escuela_id']);
        }

        if (isset($filters['estado_cupof'])) {
            $query->where('estado_cupof', $filters['estado_cupof']);
        }

        if (isset($filters['escalafon'])) {
            $query->where('escalafon', $filters['escalafon']);
        }

        return $query->get();
    }

    /**
     * Create a new CUPOF slot.
     */
    public function createCupof(array $data): Cupof
    {
        return Cupof::create([
            'codigo_cupof' => $data['codigo_cupof'],
            'escuela_id' => $data['escuela_id'],
            'asignatura_id' => $data['asignatura_id'] ?? null,
            'escalafon' => $data['escalafon'],
            'tipo_puesto' => $data['tipo_puesto'],
            'cantidad' => $data['cantidad'] ?? 1,
            'estado_cupof' => 'disponible',
        ]);
    }

    /**
     * Assign a persona to a CUPOF slot.
     */
    public function assignPersona(Cupof $cupof, Persona $persona, array $details): CupofMovimiento
    {
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
        $activeCupofs = Cupof::whereHas('movimientos', function($q) use ($persona) {
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
        $tipo = strtolower($cupof->tipo_puesto);
        
        // Check hierarchical titles in tipo_puesto
        if (str_contains($tipo, 'director')) return 'director';
        if (str_contains($tipo, 'vice')) return 'vicedirector';
        if (str_contains($tipo, 'secretario')) return 'secretario';
        if (str_contains($tipo, 'prosecretario')) return 'prosecretario';
        if (str_contains($tipo, 'preceptor')) return 'preceptor';

        // Fallback to escalafon
        if ($cupof->escalafon === 'docente') return 'profesor';
        if ($cupof->escalafon === 'auxiliar') return 'auxiliar';

        return 'profesor';
    }
}
