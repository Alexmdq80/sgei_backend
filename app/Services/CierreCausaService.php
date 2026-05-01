<?php

namespace App\Services;

use App\Models\CierreCausa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CierreCausaService
{
    /**
     * Get all enrollment closure causes.
     */
    public function getAll(): Collection
    {
        return CierreCausa::orderBy('nombre')->get();
    }

    /**
     * Create a new enrollment closure cause.
     */
    public function create(array $data): CierreCausa    {
        return DB::transaction(function () use ($data) {
            return CierreCausa::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing closure cause.
     */
    public function update(CierreCausa $cierreCausa, array $data): CierreCausa
    {
        return DB::transaction(function () use ($cierreCausa, $data) {
            $cierreCausa->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $cierreCausa->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $cierreCausa;
        });
    }

    /**
     * Delete a closure cause.
     */
    public function delete(CierreCausa $cierreCausa): bool
    {
        return (bool) $cierreCausa->delete();
    }
}
