<?php

namespace App\Services;

use App\Models\Condicion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CondicionService
{
    /**
     * Get all enrollment conditions.
     */
    public function getAll(): Collection
    {
        return Condicion::orderBy('nombre')->get();
    }

    /**
     * Create a new enrollment condition.
     */
    public function create(array $data): Condicion
    {
        return DB::transaction(function () use ($data) {
            return Condicion::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing enrollment condition.
     */
    public function update(Condicion $condicion, array $data): Condicion
    {
        return DB::transaction(function () use ($condicion, $data) {
            $condicion->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $condicion->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $condicion;
        });
    }

    /**
     * Delete an enrollment condition.
     */
    public function delete(Condicion $condicion): bool
    {
        return $condicion->delete();
    }
}
