<?php

namespace App\Services;

use App\Models\Dependencia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DependenciaService
{
    /**
     * Get all dependencies.
     */
    public function getAll(): Collection
    {
        return Dependencia::orderBy('nombre')->get();
    }

    /**
     * Create a new dependency.
     */
    public function create(array $data): Dependencia
    {
        return DB::transaction(function () use ($data) {
            return Dependencia::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing dependency.
     */
    public function update(Dependencia $dependencia, array $data): Dependencia
    {
        return DB::transaction(function () use ($dependencia, $data) {
            $dependencia->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $dependencia->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $dependencia;
        });
    }

    /**
     * Delete a dependency.
     */
    public function delete(Dependencia $dependencia): bool
    {
        return (bool) $dependencia->delete();
    }
}
