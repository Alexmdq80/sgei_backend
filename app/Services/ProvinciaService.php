<?php

namespace App\Services;

use App\Models\Provincia;

class ProvinciaService
{
    /**
     * Get paginated provinces with their nation.
     */
    public function getAll(?string $search = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Provincia::with('nacion')
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('iso_id', 'like', "%{$search}%")
                ->orWhereHas('nacion', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a province by ID.
     */
    public function getById(int $id): Provincia
    {
        return Provincia::with('nacion')->findOrFail($id);
    }

    /**
     * Create a new province.
     */
    public function create(array $data): Provincia
    {
        return Provincia::create([
            'nacion_id' => $data['nacion_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? null,
            'iso_id' => $data['iso_id'] ?? null,
        ]);
    }

    /**
     * Update an existing province.
     */
    public function update(Provincia $provincia, array $data): Provincia
    {
        $provincia->update([
            'nacion_id' => $data['nacion_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? $provincia->id_georef,
            'iso_id' => $data['iso_id'] ?? $provincia->iso_id,
        ]);

        return $provincia->load('nacion');
    }

    /**
     * Delete a province.
     */
    public function delete(Provincia $provincia): bool
    {
        return $provincia->delete();
    }
}
