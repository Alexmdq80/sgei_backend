<?php

namespace App\Services;

use App\Models\ModalidadNivel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ModalidadNivelService
{
    /**
     * Get all modality-level combinations.
     */
    public function getAll(): Collection
    {
        return ModalidadNivel::with(['nivel', 'modalidad', 'escuelaTipo'])
            ->get()
            ->sortBy(function($item) {
                return ($item->nivel->nombre ?? '') . ($item->modalidad->nombre ?? '');
            })->values();
    }

    /**
     * Create a new modality-level combination.
     */
    public function create(array $data): ModalidadNivel
    {
        return DB::transaction(function () use ($data) {
            return ModalidadNivel::create([
                'nivel_id' => $data['nivel_id'],
                'modalidad_id' => $data['modalidad_id'],
                'escuela_tipo_id' => $data['escuela_tipo_id'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing combination.
     */
    public function update(ModalidadNivel $modalidadNivel, array $data): ModalidadNivel
    {
        return DB::transaction(function () use ($modalidadNivel, $data) {
            $modalidadNivel->update([
                'nivel_id' => $data['nivel_id'],
                'modalidad_id' => $data['modalidad_id'],
                'escuela_tipo_id' => $data['escuela_tipo_id'] ?? $modalidadNivel->escuela_tipo_id,
                'updated_by' => Auth::id(),
            ]);
            return $modalidadNivel;
        });
    }

    /**
     * Delete a combination.
     */
    public function delete(ModalidadNivel $modalidadNivel): bool
    {
        return $modalidadNivel->delete();
    }
}
