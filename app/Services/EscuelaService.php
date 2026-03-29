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
    public function requestJoin(Usuario $user, int $escuelaId, int $rolEscolarId = 1): void
    {
        EscuelaUsuario::updateOrCreate(
            [
                'usuario_id' => $user->id,
                'escuela_id' => $escuelaId
            ],
            [
                'rol_escolar_id' => $rolEscolarId,
                'verified_at' => null,
            ]
        );

        $user->update(['estado' => 'espera_aprobacion']);
    }

    /**
     * Cancel join request.
     */
    public function cancelJoin(Usuario $user, ?int $escuelaId = null): void
    {
        $query = EscuelaUsuario::where('usuario_id', $user->id);
        
        if ($escuelaId) {
            $query->where('escuela_id', $escuelaId);
        }

        $query->delete();

        // Si no quedan solicitudes, volver al estado inicial de post-verificación
        if (EscuelaUsuario::where('usuario_id', $user->id)->count() === 0) {
            $user->update(['estado' => 'email_verificado']);
        }
    }
}
