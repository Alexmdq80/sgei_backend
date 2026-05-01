<?php

namespace App\Services;

use App\Models\Modalidad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ModalidadService
{
    /**
     * Get all modalities.
     */
    public function getAll(): Collection
    {
        return Modalidad::orderBy('nombre')->get();
    }

    /**
     * Create a new modality.
     */
    public function create(array $data): Modalidad
    {
        return DB::transaction(function () use ($data) {
            return Modalidad::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing modality.
     */
    public function update(Modalidad $modalidad, array $data): Modalidad
    {
        return DB::transaction(function () use ($modalidad, $data) {
            $modalidad->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $modalidad->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $modalidad;
        });
    }

    /**
     * Delete a modality.
     */
    public function delete(Modalidad $modalidad): bool
    {
        return (bool) $modalidad->delete();
    }
}
