<?php

namespace App\Services;

use App\Models\Sexo;
use Illuminate\Database\Eloquent\Collection;

class SexoService
{
    /**
     * Get all sexes ordered by priority (orden) and then name.
     */
    public function getAll(): Collection
    {
        return Sexo::orderBy('orden')->orderBy('nombre')->get();
    }

    /**
     * Get a sex by ID.
     */
    public function getById(int $id): Sexo
    {
        return Sexo::findOrFail($id);
    }

    /**
     * Create a new sex record.
     */
    public function create(array $data): Sexo
    {
        return Sexo::create([
            'nombre' => $data['nombre'],
            'letra' => $data['letra'],
            'orden' => $data['orden'] ?? 100,
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing sex record.
     */
    public function update(Sexo $sexo, array $data): Sexo
    {
        $sexo->update([
            'nombre' => $data['nombre'],
            'letra' => $data['letra'] ?? $sexo->letra,
            'orden' => $data['orden'] ?? $sexo->orden,
            'vigente' => $data['vigente'] ?? $sexo->vigente,
        ]);

        return $sexo;
    }

    /**
     * Delete a sex record.
     */
    public function delete(Sexo $sexo): bool
    {
        return $sexo->delete();
    }
}
