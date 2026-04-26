<?php

namespace App\Services;

use App\Models\Ambito;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AmbitoService
{
    /**
     * Get all ambits.
     */
    public function getAll(): Collection
    {
        return Ambito::orderBy('nombre')->get();
    }

    /**
     * Create a new ambit.
     */
    public function create(array $data): Ambito
    {
        return DB::transaction(function () use ($data) {
            return Ambito::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing ambit.
     */
    public function update(Ambito $ambito, array $data): Ambito
    {
        return DB::transaction(function () use ($ambito, $data) {
            $ambito->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $ambito->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $ambito;
        });
    }

    /**
     * Delete an ambit.
     */
    public function delete(Ambito $ambito): bool
    {
        return $ambito->delete();
    }
}
