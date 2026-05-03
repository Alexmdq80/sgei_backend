<?php

namespace App\Services;

use App\Models\Vinculo;
use Illuminate\Database\Eloquent\Collection;

class VinculoService
{
    public function getAll(): Collection
    {
        return Vinculo::with('vinculoTipo')->orderBy('nombre')->get();
    }

    public function getById(int $id): Vinculo
    {
        return Vinculo::with('vinculoTipo')->findOrFail($id);
    }

    public function create(array $data): Vinculo
    {
        return Vinculo::create($data);
    }

    public function update(Vinculo $vinculo, array $data): Vinculo
    {
        $vinculo->update($data);
        return $vinculo->load('vinculoTipo');
    }

    public function delete(Vinculo $vinculo): bool
    {
        return (bool) $vinculo->delete();
    }
}
