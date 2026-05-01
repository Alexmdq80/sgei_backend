<?php

namespace App\Services;

use App\Models\GeorefCategoria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GeorefCategoriaService
{
    /**
     * Get all Georef categories with search and pagination.
     */
    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = GeorefCategoria::query()->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'LIKE', "%" . mb_strtoupper($search) . "%");
        }

        return $perPage > 0 ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get a Georef category by ID.
     */
    public function getById(int $id): GeorefCategoria
    {
        return GeorefCategoria::findOrFail($id);
    }

    /**
     * Create a new Georef category.
     */
    public function create(array $data): GeorefCategoria
    {
        return GeorefCategoria::create([
            'nombre' => mb_strtoupper($data['nombre']),
            'orden' => $data['orden'] ?? null,
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing Georef category.
     */
    public function update(GeorefCategoria $categoria, array $data): GeorefCategoria
    {
        $categoria->update([
            'nombre' => mb_strtoupper($data['nombre']),
            'orden' => $data['orden'] ?? $categoria->orden,
            'vigente' => $data['vigente'] ?? $categoria->vigente,
        ]);

        return $categoria;
    }

    /**
     * Delete a Georef category.
     */
    public function delete(GeorefCategoria $categoria): bool
    {
        return (bool) $categoria->delete();
    }
}
