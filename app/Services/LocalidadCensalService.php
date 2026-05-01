<?php

namespace App\Services;

use App\Models\LocalidadCensal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocalidadCensalService
{
    /**
     * Get paginated census localities with their georef relationships.
     */
    public function getAll(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = LocalidadCensal::with(['georefFuente', 'georefCategoria', 'georefFuncion'])
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('id_georef', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a census locality by ID.
     */
    public function getById(int $id): LocalidadCensal
    {
        return LocalidadCensal::with(['georefFuente', 'georefCategoria', 'georefFuncion'])->findOrFail($id);
    }

    /**
     * Create a new census locality.
     */
    public function create(array $data): LocalidadCensal
    {
        return LocalidadCensal::create([
            'id_georef' => $data['id_georef'] ?? null,
            'georef_fuente_id' => $data['georef_fuente_id'] ?? null,
            'georef_categoria_id' => $data['georef_categoria_id'] ?? null,
            'georef_funcion_id' => $data['georef_funcion_id'] ?? null,
            'nombre' => mb_strtoupper($data['nombre']),
            'centroide_lat' => $data['centroide_lat'] ?? null,
            'centroide_lon' => $data['centroide_lon'] ?? null,
        ]);
    }

    /**
     * Update an existing census locality.
     */
    public function update(LocalidadCensal $localidadCensal, array $data): LocalidadCensal
    {
        $localidadCensal->update([
            'id_georef' => $data['id_georef'] ?? $localidadCensal->id_georef,
            'georef_fuente_id' => $data['georef_fuente_id'] ?? $localidadCensal->georef_fuente_id,
            'georef_categoria_id' => $data['georef_categoria_id'] ?? $localidadCensal->georef_categoria_id,
            'georef_funcion_id' => $data['georef_funcion_id'] ?? $localidadCensal->georef_funcion_id,
            'nombre' => mb_strtoupper($data['nombre']),
            'centroide_lat' => $data['centroide_lat'] ?? $localidadCensal->centroide_lat,
            'centroide_lon' => $data['centroide_lon'] ?? $localidadCensal->centroide_lon,
        ]);

        return $localidadCensal->load(['georefFuente', 'georefCategoria', 'georefFuncion']);
    }

    /**
     * Delete a census locality.
     */
    public function delete(LocalidadCensal $localidadCensal): bool
    {
        return (bool) $localidadCensal->delete();
    }
}
