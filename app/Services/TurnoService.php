<?php

namespace App\Services;

use App\Models\Turno;
use Illuminate\Database\Eloquent\Collection;

class TurnoService
{
    public function getAll(): Collection
    {
        return Turno::orderBy('orden')->orderBy('nombre')->get();
    }

    public function getById(int $id): Turno
    {
        return Turno::findOrFail($id);
    }

    public function create(array $data): Turno
    {
        return Turno::create($data);
    }

    public function update(Turno $turno, array $data): Turno
    {
        $turno->update($data);
        return $turno;
    }

    public function delete(Turno $turno): bool
    {
        return (bool) $turno->delete();
    }
}
