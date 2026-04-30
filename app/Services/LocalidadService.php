<?php

namespace App\Services;

use App\Models\Localidad;

class LocalidadService
{
    /**
     * Get paginated localities with their department, province, and nation.
     */
    public function getAll(?string $search = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Localidad::with(['departamento.provincia.nacion', 'localidadCensal'])
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhereHas('departamento', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhereHas('provincia', function ($q2) use ($search) {
                            $q2->where('nombre', 'like', "%{$search}%");
                        });
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a locality by ID.
     */
    public function getById(int $id): Localidad
    {
        return Localidad::with(['departamento.provincia.nacion', 'localidadCensal'])->findOrFail($id);
    }

    /**
     * Create a new locality.
     */
    public function create(array $data): Localidad
    {
        return Localidad::create([
            'departamento_id' => $data['departamento_id'],
            'localidad_censal_id' => $data['localidad_censal_id'] ?? null,
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? null,
        ]);
    }

    /**
     * Update an existing locality.
     */
    public function update(Localidad $localidad, array $data): Localidad
    {
        $localidad->update([
            'departamento_id' => $data['departamento_id'],
            'localidad_censal_id' => $data['localidad_censal_id'] ?? $localidad->localidad_censal_id,
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? $localidad->id_georef,
        ]);

        return $localidad->load(['departamento.provincia.nacion', 'localidadCensal']);
    }

    /**
     * Delete a locality.
     */
    public function delete(Localidad $localidad): bool
    {
        return $localidad->delete();
    }
}
