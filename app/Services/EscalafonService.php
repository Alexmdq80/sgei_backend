<?php

namespace App\Services;

use App\Models\Escalafon;
use Illuminate\Database\Eloquent\Collection;

class EscalafonService
{
    public function getAll(): Collection
    {
        return Escalafon::orderBy('orden')->orderBy('nombre')->get();
    }

    public function getById(int $id): Escalafon
    {
        return Escalafon::findOrFail($id);
    }

    public function create(array $data): Escalafon
    {
        return Escalafon::create($data);
    }

    public function update(Escalafon $escalafon, array $data): Escalafon
    {
        $escalafon->update($data);
        return $escalafon;
    }

    public function delete(Escalafon $escalafon): bool
    {
        return (bool) $escalafon->delete();
    }
}
