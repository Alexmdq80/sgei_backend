<?php

namespace App\Services;

use App\Models\GeorefFuncion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GeorefFuncionService
{
    /**
     * Get all Georef functions with search and pagination.
     */
    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = GeorefFuncion::query()->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'LIKE', "%" . mb_strtoupper($search) . "%");
        }

        return $perPage > 0 ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get a Georef function by ID.
     */
    public function getById(int $id): GeorefFuncion
    {
        return GeorefFuncion::findOrFail($id);
    }

    /**
     * Create a new Georef function.
     */
    public function create(array $data): GeorefFuncion
    {
        return GeorefFuncion::create([
            'nombre' => mb_strtoupper($data['nombre']),
            'orden' => $data['orden'] ?? null,
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing Georef function.
     */
    public function update(GeorefFuncion $funcion, array $data): GeorefFuncion
    {
        $funcion->update([
            'nombre' => mb_strtoupper($data['nombre']),
            'orden' => $data['orden'] ?? $funcion->orden,
            'vigente' => $data['vigente'] ?? $funcion->vigente,
        ]);

        return $funcion;
    }

    /**
     * Delete a Georef function.
     */
    public function delete(GeorefFuncion $funcion): bool
    {
        return $funcion->delete();
    }
}
