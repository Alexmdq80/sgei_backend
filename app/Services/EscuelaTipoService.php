<?php

namespace App\Services;

use App\Models\EscuelaTipo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EscuelaTipoService
{
    /**
     * Get all school types.
     */
    public function getAll(): Collection
    {
        return EscuelaTipo::orderBy('nombre')->get();
    }

    /**
     * Create a new school type.
     */
    public function create(array $data): EscuelaTipo
    {
        return DB::transaction(function () use ($data) {
            return EscuelaTipo::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing school type.
     */
    public function update(EscuelaTipo $escuelaTipo, array $data): EscuelaTipo
    {
        return DB::transaction(function () use ($escuelaTipo, $data) {
            $escuelaTipo->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $escuelaTipo->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $escuelaTipo;
        });
    }

    /**
     * Delete a school type.
     */
    public function delete(EscuelaTipo $escuelaTipo): bool
    {
        return $escuelaTipo->delete();
    }
}
