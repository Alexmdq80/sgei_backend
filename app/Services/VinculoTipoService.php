<?php

namespace App\Services;

use App\Models\VinculoTipo;
use Illuminate\Database\Eloquent\Collection;

class VinculoTipoService
{
    public function getAll(): Collection
    {
        return VinculoTipo::orderBy('nombre')->get();
    }

    public function getById(int $id): VinculoTipo
    {
        return VinculoTipo::findOrFail($id);
    }

    public function create(array $data): VinculoTipo
    {
        return VinculoTipo::create($data);
    }

    public function update(VinculoTipo $vinculoTipo, array $data): VinculoTipo
    {
        $vinculoTipo->update($data);
        return $vinculoTipo;
    }

    public function delete(VinculoTipo $vinculoTipo): bool
    {
        return (bool) $vinculoTipo->delete();
    }
}
