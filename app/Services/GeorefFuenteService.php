<?php

namespace App\Services;

use App\Models\GeorefFuente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GeorefFuenteService
{
    /**
     * Get all Georef sources with search and pagination.
     */
    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = GeorefFuente::query()->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'LIKE', "%" . mb_strtoupper($search) . "%");
        }

        return $perPage > 0 ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get a Georef source by ID.
     */
    public function getById(int $id): GeorefFuente
    {
        return GeorefFuente::findOrFail($id);
    }

    /**
     * Create a new Georef source.
     */
    public function create(array $data): GeorefFuente
    {
        return GeorefFuente::create([
            'nombre' => mb_strtoupper($data['nombre']),
            'orden' => $data['orden'] ?? null,
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing Georef source.
     */
    public function update(GeorefFuente $fuente, array $data): GeorefFuente
    {
        $fuente->update([
            'nombre' => mb_strtoupper($data['nombre']),
            'orden' => $data['orden'] ?? $fuente->orden,
            'vigente' => $data['vigente'] ?? $fuente->vigente,
        ]);

        return $fuente;
    }

    /**
     * Delete a Georef source.
     */
    public function delete(GeorefFuente $fuente): bool
    {
        return (bool) $fuente->delete();
    }
}
