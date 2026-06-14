<?php

namespace App\Policies;

use App\Models\Cupof;
use App\Models\Usuario;
use App\Services\EscuelaService;
use Illuminate\Auth\Access\Response;

class CupofPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $user): bool
    {
        // 0. Superusers have total access
        if ($user->hasRole('superuser')) {
            return true;
        }

        // 1. Curricular Supervisors are forbidden from CUPOF management
        if ($user->hasRole('supervisor_curricular')) {
            return false;
        }

        // 2. Administrative hierarchy: ONLY District Chief can view (limited to hierarchical positions)
        if ($user->hasRole('jefe_distrital')) {
            return true;
        }

        // 3. School conduction (Team) can view their own school
        return $user->escuelaUsuarios()->whereNotNull('verified_at')->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $user): bool
    {
        if ($user->hasRole('superuser')) return true;

        // Hierarchical admins: ONLY District Chief
        if ($user->hasRole('jefe_distrital')) {
            return true;
        }

        // Only conduction team in their school can create new CUPOF slots
        return $this->isConduccion($user);
    }

    /**
     * Determine whether the user can assign a persona to the CUPOF.
     */
    public function assign(Usuario $user, Cupof $cupof): bool
    {
        if ($user->hasRole('superuser')) return true;

        $cargo = mb_strtolower($cupof->nombre_cargo ?? '', 'UTF-8');
        $isHierarchical = false;
        foreach (EscuelaService::HIERARCHICAL_ROLES as $role) {
            if (str_contains($cargo, $role)) {
                $isHierarchical = true;
                break;
            }
        }

        // Hierarchical admins: ONLY District Chief can assign hierarchical positions
        if ($user->hasRole('jefe_distrital')) {
            return $isHierarchical;
        }

        // Conduccion: Can manage ALL positions (hierarchical and operational) in their school
        return $this->isConduccion($user, $cupof->escuela_id);
    }

    /**
     * Determine whether the user can release/delete the CUPOF.
     */
    public function release(Usuario $user, Cupof $cupof): bool
    {
        // Follows the same logic as assignment
        return $this->assign($user, $cupof);
    }

    /**
     * Determine if the user belongs to a school's conduction team.
     */
    private function isConduccion(Usuario $user, ?int $escuelaId = null): bool
    {
        $query = $user->escuelaUsuarios()
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['director', 'vicedirector', 'secretario', 'prosecretario']);
            })
            ->whereNotNull('verified_at');

        if ($escuelaId) {
            $query->where('escuela_id', $escuelaId);
        }

        return $query->exists();
    }
}
