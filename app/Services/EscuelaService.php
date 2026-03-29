<?php

namespace App\Services;

use App\Models\Escuela;
use App\Models\Usuario;
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
        $query = Escuela::query()->select(['id', 'nombre', 'numero', 'cue_anexo', 'clave_provincial', 'localidad_id']);

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

        return $query->with('localidad:id,nombre')->limit(50)->get();
    }

    /**
     * Request to join a school.
     */
    public function requestJoin(Usuario $user, int $escuelaId, int $rolEscolarId = 1): void
    {
        DB::table('escuela_usuario')->updateOrInsert(
            ['usuario_id' => $user->id],
            [
                'id' => (string) Str::uuid(),
                'escuela_id' => $escuelaId,
                'rol_escolar_id' => $rolEscolarId,
                'verified_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $user->update(['estado' => 'espera_aprobacion']);
    }

    /**
     * Cancel join request.
     */
    public function cancelJoin(Usuario $user): void
    {
        DB::table('escuela_usuario')->where('usuario_id', $user->id)->delete();
        $user->update(['estado' => 'email_verificado']);
    }
}
