<?php

namespace App\Services;

use App\Models\Asignatura;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AsignaturaService
{
    /**
     * Get asignaturas for a specific AnioPlan.
     */
    public function getByAnioPlan(int $anioPlanId): Collection
    {
        return Asignatura::where('anio_plan_id', $anioPlanId)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Create a new asignatura.
     */
    public function create(array $data): Asignatura
    {
        return DB::transaction(function () use ($data) {
            return Asignatura::create($data);
        });
    }

    /**
     * Update an asignatura.
     */
    public function update(int $id, array $data): Asignatura
    {
        return DB::transaction(function () use ($id, $data) {
            $asignatura = Asignatura::findOrFail($id);
            $asignatura->update($data);
            return $asignatura;
        });
    }

    /**
     * Delete an asignatura.
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $asignatura = Asignatura::findOrFail($id);
            return $asignatura->delete();
        });
    }
}
