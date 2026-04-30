<?php

namespace App\Services;

use App\Models\Nacion;

class NacionService
{
    /**
     * Get paginated nations with their continent.
     */
    public function getAll(?string $search = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Nacion::with('continente')
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('nacionalidad', 'like', "%{$search}%")
                ->orWhereHas('continente', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a nation by ID.
     */
    public function getById(int $id): Nacion
    {
        return Nacion::with('continente')->findOrFail($id);
    }

    /**
     * Create a new nation.
     */
    public function create(array $data): Nacion
    {
        return Nacion::create([
            'id_georef' => $data['id_georef'] ?? null,
            'continente_id' => $data['continente_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'nacionalidad' => mb_strtoupper($data['nacionalidad']),
        ]);
    }

    /**
     * Update an existing nation.
     */
    public function update(Nacion $nacion, array $data): Nacion
    {
        $nacion->update([
            'id_georef' => $data['id_georef'] ?? $nacion->id_georef,
            'continente_id' => $data['continente_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'nacionalidad' => mb_strtoupper($data['nacionalidad']),
        ]);

        return $nacion->load('continente');
    }

    /**
     * Delete a nation.
     */
    public function delete(Nacion $nacion): bool
    {
        return $nacion->delete();
    }
}
