<?php

namespace App\Services;

use App\Models\Nacion;
use Illuminate\Database\Eloquent\Collection;

class NacionService
{
    /**
     * Get all nations with their continent, ordered by name.
     */
    public function getAll(): Collection
    {
        return Nacion::with('continente')->orderBy('nombre')->get();
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
