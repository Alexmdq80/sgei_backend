<?php

namespace App\Services;

use App\Models\Localidad;
use Illuminate\Database\Eloquent\Collection;

class LocalidadService
{
    /**
     * Get all localities with their department, province, and nation, ordered by name.
     */
    public function getAll(): Collection
    {
        return Localidad::with(['departamento.provincia.nacion'])->orderBy('nombre')->get();
    }

    /**
     * Get a locality by ID.
     */
    public function getById(int $id): Localidad
    {
        return Localidad::with(['departamento.provincia.nacion'])->findOrFail($id);
    }

    /**
     * Create a new locality.
     */
    public function create(array $data): Localidad
    {
        return Localidad::create([
            'departamento_id' => $data['departamento_id'],
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
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? $localidad->id_georef,
        ]);

        return $localidad->load(['departamento.provincia.nacion']);
    }

    /**
     * Delete a locality.
     */
    public function delete(Localidad $localidad): bool
    {
        return $localidad->delete();
    }
}
