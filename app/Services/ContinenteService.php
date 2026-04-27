<?php

namespace App\Services;

use App\Models\Continente;
use Illuminate\Database\Eloquent\Collection;

class ContinenteService
{
    /**
     * Get all continents ordered by name.
     */
    public function getAll(): Collection
    {
        return Continente::orderBy('nombre')->get();
    }

    /**
     * Get a continent by ID.
     */
    public function getById(int $id): Continente
    {
        return Continente::findOrFail($id);
    }

    /**
     * Create a new continent.
     */
    public function create(array $data): Continente
    {
        return Continente::create([
            'nombre' => $data['nombre'],
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing continent.
     */
    public function update(Continente $continente, array $data): Continente
    {
        $continente->update([
            'nombre' => $data['nombre'],
            'vigente' => $data['vigente'] ?? $continente->vigente,
        ]);

        return $continente;
    }

    /**
     * Delete a continent.
     */
    public function delete(Continente $continente): bool
    {
        return $continente->delete();
    }
}
