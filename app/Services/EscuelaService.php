<?php

namespace App\Services;

use App\Models\Escuela;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\EscuelaPersona;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\DTOs\Escuela\CreateEscuelaDTO;
use App\DTOs\Escuela\UpdateEscuelaDTO;

class EscuelaService
{
    public const HIERARCHICAL_ROLES = Usuario::ROLES_EQUIPO_CONDUCCION;
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

        if ($filters) {
            foreach ($filters as $field => $value) {
                if (!$value) continue;

                if ($field === 'departamento_id') {
                    $query->whereHas('localidad', function ($q) use ($value) {
                        $q->where('departamento_id', $value);
                    });
                } elseif ($field === 'provincia_id') {
                    $query->whereHas('localidad.departamento', function ($q) use ($value) {
                        $q->where('provincia_id', $value);
                    });
                } elseif ($field === 'localidad_id') {
                    $query->where('localidad_id', $value);
                } elseif ($field === 'nivel_id') {
                    $query->whereHas('modalidadesNiveles', function ($q) use ($value) {
                        $q->where('nivel_id', $value);
                    });
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query->orderBy('nombre')->limit(500)->get();
    }

    /**
     * Get all schools for admin panel with optional district filter, level, sector and pagination size.
     */
    public function getAllAdmin(
        string $search = null, 
        ?int $departamentoId = null, 
        int $perPage = 20, 
        ?int $nivelId = null, 
        ?int $sectorId = null,
        ?int $provinciaId = null,
        ?int $regionId = null
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $query = Escuela::with(['localidad.departamento', 'ambito', 'dependencia', 'sector']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%")
                  ->orWhere('cue_anexo', 'like', "%{$search}%");
            });
        }

        if ($departamentoId) {
            $query->whereHas('localidad', function ($q) use ($departamentoId) {
                $q->where('departamento_id', $departamentoId);
            });
        }

        if ($provinciaId) {
            $query->whereHas('localidad.departamento', function ($q) use ($provinciaId) {
                $q->where('provincia_id', $provinciaId);
            });
        }

        if ($regionId) {
            $query->whereHas('localidad.departamento', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        if ($nivelId) {
            $query->whereHas('modalidadesNiveles', function ($q) use ($nivelId) {
                $q->where('nivel_id', $nivelId);
            });
        }

        if ($sectorId) {
            $query->where('sector_id', $sectorId);
        }

        return $query->orderBy('nombre')->paginate($perPage);
    }

    /**
     * Create a new school.
     */
    public function create(CreateEscuelaDTO|array $data): Escuela
    {
        $dto = $data instanceof CreateEscuelaDTO ? $data : CreateEscuelaDTO::fromArray($data);

        return DB::transaction(function () use ($dto) {
            $escuela = Escuela::create([
                'nombre' => $dto->nombre,
                'numero' => $dto->numero,
                'cue_anexo' => $dto->cueAnexo,
                'clave_provincial' => $dto->claveProvincial,
                'localidad_id' => $dto->localidadId,
                'ambito_id' => $dto->ambitoId,
                'dependencia_id' => $dto->dependenciaId,
                'sector_id' => $dto->sectorId,
                'domicilio' => $dto->domicilio,
                'telefono' => $dto->telefono,
                'email' => $dto->email,
                'codigo_postal' => $dto->codigoPostal,
                'created_by' => auth()->id(),
            ]);

            // Manejo opcional de niveles/modalidades via pivot si se envían
            if (!empty($dto->modalidadesNivelesIds)) {
                $escuela->modalidadesNiveles()->sync($dto->modalidadesNivelesIds);
            }

            return $escuela;
        });
    }

    /**
     * Update an existing school.
     */
    public function update(Escuela $escuela, UpdateEscuelaDTO|array $data): Escuela
    {
        $dto = $data instanceof UpdateEscuelaDTO ? $data : UpdateEscuelaDTO::fromArray($data);

        return DB::transaction(function () use ($escuela, $dto) {
            $updateData = [];

            if ($dto->nombre !== null) $updateData['nombre'] = $dto->nombre;
            if ($dto->numero !== null) $updateData['numero'] = $dto->numero;
            if ($dto->cueAnexo !== null) $updateData['cue_anexo'] = $dto->cueAnexo;
            if ($dto->claveProvincial !== null) $updateData['clave_provincial'] = $dto->claveProvincial;
            if ($dto->localidadId !== null) $updateData['localidad_id'] = $dto->localidadId;
            if ($dto->ambitoId !== null) $updateData['ambito_id'] = $dto->ambitoId;
            if ($dto->dependenciaId !== null) $updateData['dependencia_id'] = $dto->dependenciaId;
            if ($dto->sectorId !== null) $updateData['sector_id'] = $dto->sectorId;
            if ($dto->domicilio !== null) $updateData['domicilio'] = $dto->domicilio;
            if ($dto->telefono !== null) $updateData['telefono'] = $dto->telefono;
            if ($dto->email !== null) $updateData['email'] = $dto->email;
            if ($dto->codigoPostal !== null) $updateData['codigo_postal'] = $dto->codigoPostal;

            $updateData['updated_by'] = auth()->id();

            $escuela->update($updateData);

            if ($dto->modalidadesNivelesIds !== null) {
                $escuela->modalidadesNiveles()->sync($dto->modalidadesNivelesIds);
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

        // 1. Jefe Distrital NO puede realizar asignaciones directas (deben ser vía CUPOF)
        if ($isJefeDistrital) {
            throw new \Exception("Acceso Denegado: Como Jefe Distrital, no puedes gestionar roles institucionales directamente desde el Padrón. Debes realizarlo a través de la Gestión de CUPOF.", 403);
        }

        // 2. Equipo de Conducción NO puede asignar cargos jerárquicos
        if ($isTargetHierarchical) {
            throw new \Exception("No tienes permisos para asignar roles jerárquicos. Esta acción está reservada para el Jefe Distrital o Superusuario.", 403);
        }

        // 3. Verificar si el admin tiene rol jerárquico en la escuela destino
        $isAdminHierarchicalInSchool = EscuelaPersona::whereHas('persona', function($q) use ($admin) {
                $q->where('usuario_id', $admin->id);
            })
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
    public function assignDirect(Persona|Usuario $target, int $escuelaId, int $roleId): EscuelaPersona
    {
        $this->validateAssignmentPermissions($escuelaId, $roleId);

        $persona = $target instanceof Persona ? $target : $target->persona;
        if (!$persona) throw new \Exception("La persona o usuario no tiene un registro de persona válido.", 422);

        $link = EscuelaPersona::updateOrCreate(
            [
                'persona_id' => $persona->id,
                'escuela_id' => $escuelaId,
                'role_id' => $roleId
            ],
            [
                'verified_at' => now(),
                'updated_by' => auth()->id()
            ]
        );

        $user = $persona->usuario;
        if ($user && $user->estado !== 'activo') {
            $user->update(['estado' => 'activo']);
        }

        return $link->load(['persona.usuario', 'escuela', 'role']);
    }

    /**
     * User requests to join a school.
     */
    public function joinSchool(Usuario $user, int $escuelaId, int $roleId): EscuelaPersona
    {
        $persona = $user->persona;
        if (!$persona) throw new \Exception("El usuario no tiene una persona vinculada.", 422);

        // Check if already linked or pending
        $existing = EscuelaPersona::where('persona_id', $persona->id)
            ->where('escuela_id', $escuelaId)
            ->first();

        if ($existing) {
            throw new \Exception("Ya tienes una solicitud activa o vinculación con esta institución.", 422);
        }

        return EscuelaPersona::create([
            'id' => Str::uuid(),
            'persona_id' => $persona->id,
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
        $persona = $user->persona;
        if (!$persona) throw new \Exception("El usuario no tiene una persona vinculada.", 404);

        $link = EscuelaPersona::where('persona_id', $persona->id)
            ->where('escuela_id', $escuelaId)
            ->whereNull('verified_at')
            ->firstOrFail();

        return (bool) $link->delete();
    }
}
