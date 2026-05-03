<?php

namespace App\Services;

use App\Models\Jornada;
use Illuminate\Database\Eloquent\Collection;

class JornadaService
{
    public function getAll(): Collection
    {
        return Jornada::orderBy('orden')->orderBy('nombre')->get();
    }

    public function getById(int $id): Jornada
    {
        return Jornada::findOrFail($id);
    }

    public function create(array $data): Jornada
    {
        return Jornada::create($data);
    }

    public function update(Jornada $jornada, array $data): Jornada
    {
        $jornada->update($data);
        return $jornada;
    }

    public function delete(Jornada $jornada): bool
    {
        return (bool) $jornada->delete();
    }
}
