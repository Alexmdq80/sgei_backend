<?php

namespace App\Services;

use App\Models\DocumentoTipo;
use Illuminate\Database\Eloquent\Collection;

class DocumentoTipoService
{
    /**
     * Get all document types ordered by name.
     */
    public function getAll(): Collection
    {
        return DocumentoTipo::orderBy('nombre')->get();
    }

    /**
     * Get a document type by ID.
     */
    public function getById(int $id): DocumentoTipo
    {
        return DocumentoTipo::findOrFail($id);
    }

    /**
     * Create a new document type.
     */
    public function create(array $data): DocumentoTipo
    {
        return DocumentoTipo::create([
            'nombre' => $data['nombre'],
            'vigente' => $data['vigente'] ?? true,
        ]);
    }

    /**
     * Update an existing document type.
     */
    public function update(DocumentoTipo $documentoTipo, array $data): DocumentoTipo
    {
        $documentoTipo->update([
            'nombre' => $data['nombre'],
            'vigente' => $data['vigente'] ?? $documentoTipo->vigente,
        ]);

        return $documentoTipo;
    }

    /**
     * Delete a document type.
     */
    public function delete(DocumentoTipo $documentoTipo): bool
    {
        return (bool) $documentoTipo->delete();
    }
}
