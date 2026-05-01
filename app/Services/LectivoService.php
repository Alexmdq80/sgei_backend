<?php

namespace App\Services;

use App\Models\Lectivo;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LectivoService
{
    /**
     * Get all academic years with optional pagination/filtering.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Lectivo::query();

        if (!empty($filters['search'])) {
            $query->where('nombre', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('anio', 'desc')->orderBy('orden', 'asc')->get();
    }

    /**
     * Create a new academic year.
     */
    public function create(array $data): Lectivo
    {
        if (empty($data['orden'])) {
            $data['orden'] = (Lectivo::max('orden') ?? 0) + 1;
        }
        
        return Lectivo::create($data);
    }

    /**
     * Update an academic year.
     */
    public function update(Lectivo $lectivo, array $data): Lectivo
    {
        $lectivo->update($data);
        return $lectivo;
    }

    /**
     * Delete an academic year (Soft Delete).
     */
    public function delete(Lectivo $lectivo): bool
    {
        return (bool) $lectivo->delete();
    }
}
