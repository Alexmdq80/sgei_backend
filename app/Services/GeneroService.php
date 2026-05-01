<?php

namespace App\Services;

use App\Models\Genero;
use Illuminate\Database\Eloquent\Collection;

class GeneroService
{
    /**
     * Get all genders ordered by priority (orden) and then name.
     */
    public function getAll(): Collection
    {
        return Genero::orderBy('orden')->orderBy('nombre')->get();
    }

    /**
     * Get a gender by ID.
     */
    public function getById(int $id): Genero
    {
        return Genero::findOrFail($id);
    }

    /**
     * Create a new gender.
     */
    public function create(array $data): Genero
    {
        return Genero::create([
            'nombre' => $data['nombre'],
            'orden' => $data['orden'] ?? 100,
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing gender.
     */
    public function update(Genero $genero, array $data): Genero
    {
        $genero->update([
            'nombre' => $data['nombre'],
            'orden' => $data['orden'] ?? $genero->orden,
            'vigente' => $data['vigente'] ?? $genero->vigente,
        ]);

        return $genero;
    }

    /**
     * Delete a gender.
     */
    public function delete(Genero $genero): bool
    {
        return (bool) $genero->delete();
    }
}
