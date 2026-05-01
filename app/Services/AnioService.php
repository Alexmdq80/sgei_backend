<?php

namespace App\Services;

use App\Models\Anio;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnioService
{
    /**
     * Get all years.
     */
    public function getAll(): Collection
    {
        return Anio::orderBy('anio_absoluto')->get();
    }

    /**
     * Get active years.
     */
    public function getActive(): Collection
    {
        return Anio::where('vigente', true)->orderBy('anio_absoluto')->get();
    }

    /**
     * Create a new year.
     */
    public function create(array $data): Anio
    {
        return DB::transaction(function () use ($data) {
            return Anio::create([
                'nombre' => $data['nombre'],
                'nombre_completo' => $data['nombre_completo'] ?? $data['nombre'],
                'anio_absoluto' => $data['anio_absoluto'] ?? 0,
                'anio_relativo' => $data['anio_relativo'] ?? 0,
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing year.
     */
    public function update(Anio $anio, array $data): Anio
    {
        return DB::transaction(function () use ($anio, $data) {
            $anio->update([
                'nombre' => $data['nombre'],
                'nombre_completo' => $data['nombre_completo'] ?? $data['nombre'],
                'anio_absoluto' => $data['anio_absoluto'] ?? $anio->anio_absoluto,
                'anio_relativo' => $data['anio_relativo'] ?? $anio->anio_relativo,
                'vigente' => $data['vigente'] ?? $anio->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $anio;
        });
    }

    /**
     * Delete a year.
     */
    public function delete(Anio $anio): bool
    {
        return (bool) $anio->delete();
    }
}
