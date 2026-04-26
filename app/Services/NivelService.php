<?php

namespace App\Services;

use App\Models\Nivel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NivelService
{
    /**
     * Get all levels.
     */
    public function getAll(): Collection
    {
        return Nivel::orderBy('nombre')->get();
    }

    /**
     * Create a new level.
     */
    public function create(array $data): Nivel
    {
        return DB::transaction(function () use ($data) {
            return Nivel::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing level.
     */
    public function update(Nivel $nivel, array $data): Nivel
    {
        return DB::transaction(function () use ($nivel, $data) {
            $nivel->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $nivel->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $nivel;
        });
    }

    /**
     * Delete a level.
     */
    public function delete(Nivel $nivel): bool
    {
        return $nivel->delete();
    }
}
