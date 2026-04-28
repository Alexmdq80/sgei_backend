<?php

namespace App\Services;

use App\Models\Departamento;
use Illuminate\Database\Eloquent\Collection;

class DepartamentoService
{
    /**
     * Get all departments with their province and nation, ordered by name.
     */
    public function getAll(): Collection
    {
        return Departamento::with(['provincia.nacion'])->orderBy('nombre')->get();
    }

    /**
     * Get a department by ID.
     */
    public function getById(int $id): Departamento
    {
        return Departamento::with(['provincia.nacion'])->findOrFail($id);
    }

    /**
     * Create a new department.
     */
    public function create(array $data): Departamento
    {
        return Departamento::create([
            'provincia_id' => $data['provincia_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? null,
        ]);
    }

    /**
     * Update an existing department.
     */
    public function update(Departamento $departamento, array $data): Departamento
    {
        $departamento->update([
            'provincia_id' => $data['provincia_id'],
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? $departamento->id_georef,
        ]);

        return $departamento->load(['provincia.nacion']);
    }

    /**
     * Delete a department.
     */
    public function delete(Departamento $departamento): bool
    {
        return $departamento->delete();
    }
}
