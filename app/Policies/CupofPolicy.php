<?php

namespace App\Policies;

use App\Models\Cupof;
use App\Models\Usuario;
use App\Policies\Concerns\HasSuperUserAccess;
use App\Services\EscuelaService;
use Illuminate\Support\Str;

class CupofPolicy
{
    use HasSuperUserAccess;

    /**
     * Determine whether the user can view any models.
     * Superuser autorizado automáticamente por before().
     */
    public function viewAny(Usuario $user): bool
    {
        // 1. El Equipo de Conducción siempre tiene acceso a los CUPOF de su escuela
        if ($this->isConduccion($user)) {
            return true;
        }

        // 2. Supervisores curriculares no tienen acceso a gestión de CUPOF
        if ($user->hasRole('supervisor_curricular')) {
            return false;
        }

        // 3. Jerarquía administrativa: Sólo Jefe Distrital
        return $user->hasRole('jefe_distrital');
    }

    /**
     * Determine whether the user can create models.
     * Superuser autorizado automáticamente por before().
     */
    public function create(Usuario $user): bool
    {
        return $this->isConduccion($user) || $user->hasRole('jefe_distrital');
    }

    /**
     * Determine whether the user can assign a persona to the CUPOF.
     * Superuser autorizado automáticamente por before().
     */
    public function assign(Usuario $user, Cupof $cupof): bool
    {
        $cargo = mb_strtolower($cupof->nombre_cargo ?? '', 'UTF-8');

        // Str::contains evalúa automáticamente si el cargo contiene cualquiera de los roles del array
        $isHierarchical = Str::contains($cargo, EscuelaService::HIERARCHICAL_ROLES);

        // Jerarquía administrativa: SÓLO Jefe Distrital puede asignar cargos jerárquicos
        if ($user->hasRole('jefe_distrital')) {
            return $isHierarchical;
        }

        // Conducción: Puede gestionar todos los cargos dentro de su escuela
        return $this->isConduccion($user, $cupof->escuela_id);
    }

    /**
     * Determine whether the user can release/delete the CUPOF.
     */
    public function release(Usuario $user, Cupof $cupof): bool
    {
        return $this->assign($user, $cupof);
    }

    /**
     * Determine if the user belongs to a school's conduction team.
     */
    private function isConduccion(Usuario $user, ?int $escuelaId = null): bool
    {
        $rolesConduccion = ['director', 'vicedirector', 'secretario', 'prosecretario'];

        $query = $user->persona?->escuelasPersonas()
            ->whereHas('role', fn($q) => $q->whereIn('name', $rolesConduccion))
            ->whereNotNull('verified_at');

        if ($escuelaId) {
            $query->where('escuela_id', $escuelaId);
        }

        return $query->exists() ?? false;
    }
}