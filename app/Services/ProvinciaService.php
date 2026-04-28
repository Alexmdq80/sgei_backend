<?php

namespace App\Services;

use App\Models\Provincia;
use Illuminate\Database\Eloquent\Collection;

class ProvinciaService
{
    /**
     * Get all provinces with their nation, ordered by name.
     */
    public function getAll(): Collection
    {
        return Provincia::with('nacion')->orderBy('nombre')->get();
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
