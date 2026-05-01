<?php

namespace App\Services;

use App\Models\DocumentoSituacion;
use Illuminate\Database\Eloquent\Collection;

class DocumentoSituacionService
{
    /**
     * Get all document situations ordered by name.
     */
    public function getAll(): Collection
    {
        return DocumentoSituacion::orderBy('nombre')->get();
    }

    /**
     * Get a document situation by ID.
     */
    public function getById(int $id): DocumentoSituacion
    {
        return DocumentoSituacion::findOrFail($id);
    }

    /**
     * Create a new document situation.
     */
    public function create(array $data): DocumentoSituacion
    {
        return DocumentoSituacion::create([
            'nombre' => $data['nombre'],
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing document situation.
     */
    public function update(DocumentoSituacion $documentoSituacion, array $data): DocumentoSituacion
    {
        $documentoSituacion->update([
            'nombre' => $data['nombre'],
            'vigente' => $data['vigente'] ?? $documentoSituacion->vigente,
        ]);

        return $documentoSituacion;
    }

    /**
     * Delete a document situation.
     */
    public function delete(DocumentoSituacion $documentoSituacion): bool
    {
        return (bool) $documentoSituacion->delete();
    }
}
