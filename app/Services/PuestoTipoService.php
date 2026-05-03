<?php

namespace App\Services;

use App\Models\PuestoTipo;
use Illuminate\Database\Eloquent\Collection;

class PuestoTipoService
{
    public function getAll(): Collection
    {
        return PuestoTipo::orderBy('orden')->orderBy('nombre')->get();
    }

    public function getById(int $id): PuestoTipo
    {
        return PuestoTipo::findOrFail($id);
    }

    public function create(array $data): PuestoTipo
    {
        return PuestoTipo::create($data);
    }

    public function update(PuestoTipo $puestoTipo, array $data): PuestoTipo
    {
        $puestoTipo->update($data);
        return $puestoTipo;
    }

    public function delete(PuestoTipo $puestoTipo): bool
    {
        return (bool) $puestoTipo->delete();
    }
}
