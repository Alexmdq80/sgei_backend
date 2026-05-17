<?php

namespace App\Services;

use App\Models\Departamento;

class DepartamentoService
{
    /**
     * Get paginated departments with their province and nation.
     */
    public function getAll(?string $search = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Departamento::with(['provincia.nacion', 'region'])
            ->orderBy('nombre');

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhereHas('provincia', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhereHas('nacion', function ($q2) use ($search) {
                            $q2->where('nombre', 'like', "%{$search}%");
                        });
                })
                ->orWhereHas('region', function ($q) use ($search) {
                    $q->where('numero', 'like', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a department by ID.
     */
    public function getById(int $id): Departamento
    {
        return Departamento::with(['provincia.nacion', 'region'])->findOrFail($id);
    }

    /**
     * Create a new department.
     */
    public function create(array $data): Departamento
    {
        return Departamento::create([
            'provincia_id' => $data['provincia_id'],
            'region_id' => $data['region_id'] ?? null,
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? null,
        ]);
    }

    /**
     * Update an existing department.
     */
    public function update(Departamento $departamento, array $data): Departamento
    {
        $departamento->update([
            'provincia_id' => $data['provincia_id'],
            'region_id' => $data['region_id'] ?? $departamento->region_id,
            'nombre' => mb_strtoupper($data['nombre']),
            'id_georef' => $data['id_georef'] ?? $departamento->id_georef,
        ]);

        return $departamento->load(['provincia.nacion', 'region']);
    }

    /**
     * Delete a department.
     */
    public function delete(Departamento $departamento): bool
    {
        return (bool) $departamento->delete();
    }
}
