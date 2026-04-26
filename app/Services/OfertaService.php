<?php

namespace App\Services;

use App\Models\Oferta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OfertaService
{
    /**
     * Get all offers.
     */
    public function getAll(): Collection
    {
        return Oferta::orderBy('nombre')->get();
    }

    /**
     * Create a new offer.
     */
    public function create(array $data): Oferta
    {
        return DB::transaction(function () use ($data) {
            return Oferta::create([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? true,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing offer.
     */
    public function update(Oferta $oferta, array $data): Oferta
    {
        return DB::transaction(function () use ($oferta, $data) {
            $oferta->update([
                'nombre' => $data['nombre'],
                'vigente' => $data['vigente'] ?? $oferta->vigente,
                'updated_by' => Auth::id(),
            ]);
            return $oferta;
        });
    }

    /**
     * Delete an offer.
     */
    public function delete(Oferta $oferta): bool
    {
        return $oferta->delete();
    }
}
