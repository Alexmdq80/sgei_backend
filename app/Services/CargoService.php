<?php

namespace App\Services;

use App\Models\Cargo;
use Illuminate\Database\Eloquent\Collection;

class CargoService
{
    /**
     * Get all active cargos.
     */
    public function listActive(): Collection
    {
        return Cargo::with('escalafon')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Find a cargo by ID.
     */
    public function findById(int $id): ?Cargo
    {
        return Cargo::find($id);
    }

    /**
     * Create a new cargo.
     */
    public function store(array $data): Cargo
    {
        return Cargo::create($data);
    }

    /**
     * Update an existing cargo.
     */
    public function update(Cargo $cargo, array $data): Cargo
    {
        $cargo->update($data);
        return $cargo;
    }

    /**
     * Delete a cargo.
     */
    public function delete(Cargo $cargo): bool
    {
        return (bool) $cargo->delete();
    }
}
