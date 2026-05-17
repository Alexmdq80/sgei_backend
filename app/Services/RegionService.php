<?php

namespace App\Services;

use App\Models\Region;

class RegionService
{
    /**
     * Get paginated regions with their province.
     */
    public function getAll(?string $search = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Region::with('provincia')
            ->orderBy('numero');

        if ($search) {
            $query->where('numero', 'like', "%{$search}%")
                ->orWhereHas('provincia', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a region by ID.
     */
    public function getById(int $id): Region
    {
        return Region::with('provincia')->findOrFail($id);
    }

    /**
     * Create a new region.
     */
    public function create(array $data): Region
    {
        return Region::create($data);
    }

    /**
     * Update an existing region.
     */
    public function update(Region $region, array $data): Region
    {
        $region->update($data);
        return $region->load('provincia');
    }

    /**
     * Delete a region.
     */
    public function delete(Region $region): bool
    {
        return (bool) $region->delete();
    }
}
