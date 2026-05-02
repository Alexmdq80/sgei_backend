<?php

namespace App\Services;

use App\Models\Municipio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MunicipioService
{
    /**
     * Get paginated municipalities with their province and nation.
     */
    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Municipio::with(['provincia.nacion'])
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhereHas('provincia', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhereHas('nacion', function ($q2) use ($search) {
                            $q2->where('nombre', 'like', "%{$search}%");
                        });
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a municipality by ID.
     */
    public function getById(int $id): Municipio
    {
        return Municipio::with(['provincia.nacion'])->findOrFail($id);
    }

    /**
     * Create a new municipality.
     */
    public function create(array $data): Municipio
    {
        return Municipio::create([
            'provincia_id' => $data['provincia_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? null,
        ]);
    }

    /**
     * Update an existing municipality.
     */
    public function update(Municipio $municipio, array $data): Municipio
    {
        $municipio->update([
            'provincia_id' => $data['provincia_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? $municipio->id_georef,
        ]);

        return $municipio->load(['provincia.nacion']);
    }

    /**
     * Delete a municipality.
     */
    public function delete(Municipio $municipio): bool
    {
        return (bool) $municipio->delete();
    }
}
