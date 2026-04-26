<?php

namespace App\Services;

use App\Models\EscuelaUbicacion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EscuelaUbicacionService
{
    /**
     * Get all school locations.
     */
    public function getAll(): Collection
    {
        return EscuelaUbicacion::orderBy('nombre')->get();
    }

    /**
     * Create a new school location.
     */
    public function create(array $data): EscuelaUbicacion
    {
        return DB::transaction(function () use ($data) {
            return EscuelaUbicacion::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing school location.
     */
    public function update(EscuelaUbicacion $escuelaUbicacion, array $data): EscuelaUbicacion
    {
        return DB::transaction(function () use ($escuelaUbicacion, $data) {
            $escuelaUbicacion->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $escuelaUbicacion->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $escuelaUbicacion;
        });
    }

    /**
     * Delete a school location.
     */
    public function delete(EscuelaUbicacion $escuelaUbicacion): bool
    {
        return $escuelaUbicacion->delete();
    }
}
