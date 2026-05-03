<?php

namespace App\Services;

use App\Models\Calle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CalleService
{
    /**
     * Get paginated calles with their census locality.
     */
    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Calle::with(['localidadCensal'])
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhereHas('localidadCensal', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a calle by ID.
     */
    public function getById(int $id): Calle
    {
        return Calle::with(['localidadCensal'])->findOrFail($id);
    }

    /**
     * Create a new calle.
     */
    public function create(array $data): Calle
    {
        return Calle::create([
            'nombre' => mb_strtoupper($data['nombre']),
            'localidad_censal_id' => $data['localidad_censal_id'],
            'id_georef' => $data['id_georef'] ?? null,
            'altura_inicio_derecha' => $data['altura_inicio_derecha'] ?? null,
            'altura_inicio_izquierda' => $data['altura_inicio_izquierda'] ?? null,
            'altura_fin_derecha' => $data['altura_fin_derecha'] ?? null,
            'altura_fin_izquierda' => $data['altura_fin_izquierda'] ?? null,
        ]);
    }

    /**
     * Update an existing calle.
     */
    public function update(Calle $calle, array $data): Calle
    {
        $calle->update([
            'nombre' => mb_strtoupper($data['nombre']),
            'localidad_censal_id' => $data['localidad_censal_id'],
            'id_georef' => $data['id_georef'] ?? $calle->id_georef,
            'altura_inicio_derecha' => $data['altura_inicio_derecha'] ?? $calle->altura_inicio_derecha,
            'altura_inicio_izquierda' => $data['altura_inicio_izquierda'] ?? $calle->altura_inicio_izquierda,
            'altura_fin_derecha' => $data['altura_fin_derecha'] ?? $calle->altura_fin_derecha,
            'altura_fin_izquierda' => $data['altura_fin_izquierda'] ?? $calle->altura_fin_izquierda,
        ]);

        return $calle->load(['localidadCensal']);
    }

    /**
     * Delete a calle.
     */
    public function delete(Calle $calle): bool
    {
        return (bool) $calle->delete();
    }
}
