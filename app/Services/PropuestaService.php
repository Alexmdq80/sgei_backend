<?php

namespace App\Services;

use App\Models\Propuesta;
use App\Models\Usuario;
use App\Models\Escuela;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropuestaService
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Get authorized schools for the user.
     */
    public function getAuthorizedSchools(Usuario $user): Collection
    {
        // Superuser no tiene restricción: puede acceder a todas las escuelas.
        if ($user->hasRole('superuser')) {
            return Escuela::orderBy('nombre')->get(['id', 'nombre', 'numero']);
        }

        return $this->userService->getAuthorizedSchoolsForProposals($user);
    }
    /**
     * Get all proposals with relations, filtered by authorized schools if not superuser.
     */
    public function getAllPropuestas(Usuario $user, array $filters = []): Collection
    {
        $query = Propuesta::with([
            'escuela',
            'anioPlan.plan',
            'anioPlan.anio',
            'turnoInicio',
            'turnoFin',
            'jornada',
            'cicloLectivo'
        ]);

        // If not superuser, filter by authorized schools
        if (!$user->hasRole('superuser')) {
            $authorizedSchoolIds = $this->userService->getAuthorizedSchoolsForProposals($user)->pluck('id');

            // If school_id filter is provided, check if it's authorized
            if (isset($filters['escuela_id'])) {
                if (!$authorizedSchoolIds->contains($filters['escuela_id'])) {
                    throw ValidationException::withMessages([
                        'escuela_id' => ['No tienes permisos para gestionar propuestas de esta institución.']
                    ]);
                }
                $query->where('escuela_id', $filters['escuela_id']);
            } else {
                // Return proposals for all authorized schools
                $query->whereIn('escuela_id', $authorizedSchoolIds);
            }
        } else {
            // Superuser can filter by any school or see all
            if (isset($filters['escuela_id'])) {
                $query->where('escuela_id', $filters['escuela_id']);
            }
        }

        if (isset($filters['lectivo_id'])) {
            $query->where('lectivo_id', $filters['lectivo_id']);
        }

        return $query->get();
    }

    /**
     * Get a single proposal with authorization check.
     */
    public function getPropuestaById(Usuario $user, int $id): Propuesta
    {
        $propuesta = Propuesta::with([
            'escuela',
            'anioPlan.plan',
            'anioPlan.anio',
            'turnoInicio',
            'turnoFin',
            'jornada',
            'cicloLectivo'
        ])->findOrFail($id);

        if (!$user->hasRole('superuser')) {
            $authorizedSchoolIds = $this->userService->getAuthorizedSchoolsForProposals($user)->pluck('id');
            if (!$authorizedSchoolIds->contains($propuesta->escuela_id)) {
                abort(403, 'No tienes permisos para acceder a esta propuesta.');
            }
        }

        return $propuesta;
    }

    /**
     * Create a new proposal with authorization check.
     */
    public function createPropuesta(Usuario $user, array $data): Propuesta
    {
        if (!$user->hasRole('superuser')) {
            $authorizedSchoolIds = $this->userService->getAuthorizedSchoolsForProposals($user)->pluck('id');
            if (!$authorizedSchoolIds->contains($data['escuela_id'])) {
                throw ValidationException::withMessages([
                    'escuela_id' => ['No tienes permisos para crear propuestas en esta institución.']
                ]);
            }
        }

        return Propuesta::create($data);
    }

    /**
     * Update an existing proposal with authorization check.
     */
    public function updatePropuesta(Usuario $user, Propuesta $propuesta, array $data): Propuesta
    {
        if (!$user->hasRole('superuser')) {
            $authorizedSchoolIds = $this->userService->getAuthorizedSchoolsForProposals($user)->pluck('id');
            if (
                !$authorizedSchoolIds->contains($propuesta->escuela_id) ||
                (isset($data['escuela_id']) && !$authorizedSchoolIds->contains($data['escuela_id']))
            ) {
                throw ValidationException::withMessages([
                    'escuela_id' => ['No tienes permisos para modificar propuestas de esta institución.']
                ]);
            }
        }

        $propuesta->update($data);
        return $propuesta;
    }

    /**
     * Delete a proposal with authorization check.
     */
    public function deletePropuesta(Usuario $user, Propuesta $propuesta): bool
    {
        if (!$user->hasRole('superuser')) {
            $authorizedSchoolIds = $this->userService->getAuthorizedSchoolsForProposals($user)->pluck('id');
            if (!$authorizedSchoolIds->contains($propuesta->escuela_id)) {
                abort(403, 'No tienes permisos para eliminar esta propuesta.');
            }
        }

        return (bool) $propuesta->delete();
    }
}
