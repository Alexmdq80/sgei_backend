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
        $query = Escuela::query()
            ->select(['id', 'nombre', 'numero', 'cue_anexo', 'localidad_id', 'ambito_id', 'dependencia_id', 'sector_id', 'domicilio'])
            ->with(['localidad:id,nombre', 'ambito:id,nombre', 'dependencia:id,nombre', 'sector:id,nombre']);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('numero', 'like', "%{$term}%")
                  ->orWhere('cue_anexo', 'like', "%{$term}%");
            });
        }

        foreach ($filters as $field => $value) {
            if ($value) {
                $query->where($field, $value);
            }
        }

        return $query->limit(50)->get();
    }

    /**
     * Get all schools for admin panel.
     */
    public function getAllAdmin(string $search = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Escuela::with(['localidad', 'ambito', 'dependencia', 'sector']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%")
                  ->orWhere('cue_anexo', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('nombre')->paginate(20);
    }

    /**
     * Create a new school.
     */
    public function create(array $data): Escuela
    {
        return DB::transaction(function () use ($data) {
            $escuela = Escuela::create([
                'nombre' => $data['nombre'],
                'numero' => $data['numero'],
                'cue_anexo' => $data['cue_anexo'],
                'clave_provincial' => $data['clave_provincial'] ?? null,
                'localidad_id' => $data['localidad_id'],
                'ambito_id' => $data['ambito_id'] ?? null,
                'dependencia_id' => $data['dependencia_id'] ?? null,
                'sector_id' => $data['sector_id'] ?? null,
                'domicilio' => $data['domicilio'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'email' => $data['email'] ?? null,
                'codigo_postal' => $data['codigo_postal'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Manejo opcional de niveles/modalidades via pivot si se envían
            if (isset($data['modalidades_niveles_ids'])) {
                $escuela->modalidadesNiveles()->sync($data['modalidades_niveles_ids']);
            }

            return $escuela;
        });
    }

    /**
     * Update an existing school.
     */
    public function update(Escuela $escuela, array $data): Escuela
    {
        return DB::transaction(function () use ($escuela, $data) {
            $escuela->update([
                'nombre' => $data['nombre'],
                'numero' => $data['numero'],
                'cue_anexo' => $data['cue_anexo'],
                'clave_provincial' => $data['clave_provincial'] ?? $escuela->clave_provincial,
                'localidad_id' => $data['localidad_id'],
                'ambito_id' => $data['ambito_id'] ?? $escuela->ambito_id,
                'dependencia_id' => $data['dependencia_id'] ?? $escuela->dependencia_id,
                'sector_id' => $data['sector_id'] ?? $escuela->sector_id,
                'domicilio' => $data['domicilio'] ?? $escuela->domicilio,
                'telefono' => $data['telefono'] ?? $escuela->telefono,
                'email' => $data['email'] ?? $escuela->email,
                'codigo_postal' => $data['codigo_postal'] ?? $escuela->codigo_postal,
                'updated_by' => auth()->id(),
            ]);

            if (isset($data['modalidades_niveles_ids'])) {
                $escuela->modalidadesNiveles()->sync($data['modalidades_niveles_ids']);
            }

            return $escuela;
        });
    }

    /**
     * Delete a school.
     */
    public function delete(Escuela $escuela): bool
    {
        return (bool) $escuela->delete();
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

        // NUNCA permitir asignar el rol de superuser a través de viculaciones escolares
        if ($role->name === 'superuser') {
            throw new \Exception("El rol de Superusuario no puede ser asignado institucionalmente.", 403);
        }

        // REGLA ESTRICTA: Superusuario NO puede asignar roles institucionales directamente.
        if ($isSuperUser) {
            throw new \Exception("Acceso Denegado: Como Superusuario, no puedes asignar roles institucionales directamente. Esta acción está reservada para el Jefe Distrital o el Equipo de Conducción.", 403);
        }

        $isTargetHierarchical = in_array($role->name, self::HIERARCHICAL_ROLES);

        // 1. Jefe Distrital puede asignar cargos jerárquicos (Equipo de Conducción)
        if ($isJefeDistrital) {
            if (!$isTargetHierarchical) {
                throw new \Exception("Como Jefe Distrital, solo tienes permitido asignar roles del Equipo de Conducción.", 403);
            }
            return;
        }

        // 2. Equipo de Conducción NO puede asignar cargos jerárquicos
        if ($isTargetHierarchical) {
            throw new \Exception("No tienes permisos para asignar roles jerárquicos. Esta acción está reservada para el Jefe Distrital o Superusuario.", 403);
        }

        // 3. Verificar si el admin tiene rol jerárquico en la escuela destino
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

    /**
     * User requests to join a school.
     */
    public function joinSchool(Usuario $user, int $escuelaId, int $roleId): EscuelaUsuario
    {
        // Check if already linked or pending
        $existing = EscuelaUsuario::where('usuario_id', $user->id)
            ->where('escuela_id', $escuelaId)
            ->first();

        if ($existing) {
            throw new \Exception("Ya tienes una solicitud activa o vinculación con esta institución.", 422);
        }

        return EscuelaUsuario::create([
            'id' => Str::uuid(),
            'usuario_id' => $user->id,
            'escuela_id' => $escuelaId,
            'role_id' => $roleId,
            'verified_at' => null // Pending admin confirmation
        ]);
    }

    /**
     * User cancels their own join request.
     */
    public function cancelJoinRequest(Usuario $user, int $escuelaId): bool
    {
        $link = EscuelaUsuario::where('usuario_id', $user->id)
            ->where('escuela_id', $escuelaId)
            ->whereNull('verified_at')
            ->firstOrFail();

        return (bool) $link->delete();
    }
}
