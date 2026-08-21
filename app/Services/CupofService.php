<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Cupof;
use App\Models\CupofMovimiento;
use App\Notifications\CupofAssignmentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;
use App\DTOs\Cupof\CreateCupofDTO;

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
        $isHierarchicalAdmin = $user->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital']);
        $query = Cupof::with(['escuela', 'asignatura', 'escalafon', 'puestoTipo', 'movimientoActivo.persona']);

        // Bypass for Superusers: see everything
        if ($isSuperUser) {
            // No filter applied here, proceeds to global filters
        }
        // 1. Restriction for Hierarchical Admins: Only see hierarchical positions within their jurisdiction
        elseif ($isHierarchicalAdmin) {
            $hierarchicalKeywords = \App\Services\EscuelaService::HIERARCHICAL_ROLES;
            $query->where(function ($q) use ($hierarchicalKeywords) {
                foreach ($hierarchicalKeywords as $keyword) {
                    $q->orWhere('nombre_cargo', 'like', "%{$keyword}%");
                }
            });   
        } 
        // 2. Restriction for Conduction Team: Only see their own school(s)
        else {
            $schoolIds = $user->escuelasPersonas()
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

        if (isset($filters['localidad_id']) && !empty($filters['localidad_id'])) {
            $query->whereHas('escuela', function ($q) use ($filters) {
                $q->where('localidad_id', $filters['localidad_id']);
            });
        }

        if (isset($filters['nivel_id']) && !empty($filters['nivel_id'])) {
            $query->whereHas('escuela.modalidadesNiveles', function ($q) use ($filters) {
                $q->where('nivel_id', $filters['nivel_id']);
            });
        }

        if (isset($filters['sector_id']) && !empty($filters['sector_id'])) {
            $query->whereHas('escuela', function ($q) use ($filters) {
                $q->where('sector_id', $filters['sector_id']);
            });
        }

        if (isset($filters['numero']) && !empty($filters['numero'])) {
            $query->whereHas('escuela', function ($q) use ($filters) {
                $q->where('numero', $filters['numero']);
            });
        }

        if (isset($filters['school_name']) && !empty($filters['school_name'])) {
            $query->whereHas('escuela', function ($q) use ($filters) {
                $q->where('nombre', 'like', "%{$filters['school_name']}%");
            });
        }

        return $query->join('escuelas', 'cupofs.escuela_id', '=', 'escuelas.id')
            ->orderBy('escuelas.nombre')
            ->select('cupofs.*')
            ->get();
    }

    /**
     * Create a new CUPOF slot.
     */
    public function createCupof(CreateCupofDTO|array $data): Cupof
    {
        $dto = $data instanceof CreateCupofDTO ? $data : CreateCupofDTO::fromArray($data);

        $this->validateHierarchicalAccess($dto->nombreCargo ?? '', $dto->escuelaId);

        return Cupof::create([
            'codigo_cupof' => $dto->codigoCupof,
            'escuela_id' => $dto->escuelaId,
            'asignatura_id' => $dto->asignaturaId,
            'nombre_cargo' => $dto->nombreCargo,
            'escalafon_id' => $dto->escalafonId,
            'puesto_tipo_id' => $dto->puestoTipoId,
            'cantidad' => $dto->cantidad,
            'estado_cupof' => $dto->estadoCupof,
        ]);
    }

    /**
     * Assign a persona to a CUPOF slot.
     */
    public function assignPersona(Cupof $cupof, Persona $persona, array $details): array
    {
        $this->validateHierarchicalAccess($cupof->nombre_cargo ?? '', $cupof->escuela_id);

        $movimiento = DB::transaction(function () use ($cupof, $persona, $details) {
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
            $this->syncEscuelaPersona($cupof, $persona);

            return $movimiento;
        });

        // Try to send notification
        $email = $persona->usuario?->email ?? $persona->contacto?->email;
        $notificationSent = false;

        if ($email) {
            try {
                $personaNombre = $persona->nombre . ' ' . $persona->apellido;
                if ($persona->usuario) {
                    $persona->usuario->notify(new CupofAssignmentNotification($cupof, $details['situacion_revista'], $personaNombre));
                } else {
                    Notification::route('mail', $email)
                        ->notify(new CupofAssignmentNotification($cupof, $details['situacion_revista'], $personaNombre));
                }
                $notificationSent = true;
            } catch (\Exception $e) {
                Log::error("Error enviando notificación de asignación CUPOF: " . $e->getMessage());
            }
        }

        return [
            'movimiento' => $movimiento,
            'notification_sent' => $notificationSent,
            'email_found' => !empty($email)
        ];
    }

    /**
     * Release a CUPOF slot (e.g. resignation or section closure).
     */
    public function releaseCupof(Cupof $cupof, ?string $motivoBaja = null): bool
    {
        $this->validateHierarchicalAccess($cupof->nombre_cargo ?? '', $cupof->escuela_id);

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
                $this->syncEscuelaPersona($cupof, $persona, true);
            }

            return $updated;
        });
    }

    /**
     * Validates if the current user has permissions to manage the given cargo name and school.
     */
    private function validateHierarchicalAccess(string $nombreCargo, ?int $escuelaId = null): void
    {
        $user = auth()->user();
        if (!$user) throw new \Exception("Usuario no autenticado", 401);

        // Bypass for Superusers: Total access
        if ($user->hasRole('superuser')) return;
              
        $isHierarchical = false;
        $nombreCargoLower = mb_strtolower($nombreCargo, 'UTF-8');
        foreach (\App\Services\EscuelaService::HIERARCHICAL_ROLES as $role) {
            if (str_contains($nombreCargoLower, $role)) {
                $isHierarchical = true;
                break;
            }
        }

        if ($isHierarchicalAdmin) {
            if (!$isHierarchical) {
                throw new \Exception("Como cargo jerárquico administrativo, solo tienes permitido gestionar cargos del Equipo de Conducción.", 403);
            }
            
        } else {
            // Equipo de Conducción (Director, Vice, etc.)
            // Validation is mostly handled by CupofPolicy or previous checks
        }
    }

    /**
     * Syncs the EscuelaPersona record based on CUPOF assignments.
     */
    private function syncEscuelaPersona(Cupof $cupof, Persona $persona, bool $isRelease = false): void
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
                \App\Models\EscuelaPersona::where('persona_id', $persona->id)
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
     * Syncs all school-user links and roles for a user based on all active CUPOF movements.
     * Useful after a persona is linked to a user account.
     */
    public function syncAllRolesFromCupof(Usuario $usuario): void
    {
        $persona = $usuario->persona;
        if (!$persona) return;

        $schoolIds = $persona->movimientosCupofActivos()
            ->join('cupofs', 'cupof_movimientos.cupof_id', '=', 'cupofs.id')
            ->distinct()
            ->pluck('cupofs.escuela_id');

        foreach ($schoolIds as $escuelaId) {
            $this->refreshUserRoleInSchool($usuario, $escuelaId, $persona);
        }
    }

    /**
     * Determines and syncs all roles the user has in a school based on all active CUPOFs.
     */
    public function refreshUserRoleInSchool($usuario, $escuelaId, $persona): void
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

            \App\Models\EscuelaPersona::updateOrCreate(
                [
                    'persona_id' => $persona->id,
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
        \App\Models\EscuelaPersona::where('persona_id', $persona->id)
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
    public function mapCupofToRole(Cupof $cupof): string
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
