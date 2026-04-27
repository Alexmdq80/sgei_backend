<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentoTipo;
use App\Services\DocumentoTipoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DocumentoTipoController extends Controller
{
    public function __construct(
        protected DocumentoTipoService $documentoTipoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Para administración devolvemos todo. 
        // Si es la ruta pública, podríamos filtrar por vigente, pero el Service getAll devuelve todo ordenado.
        return response()->json($this->documentoTipoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:documento_tipos,nombre',
            'vigente' => 'boolean'
        ]);

        $item = $this->documentoTipoService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->documentoTipoService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentoTipo $documentoTipo): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:documento_tipos,nombre,' . $documentoTipo->id,
            'vigente' => 'boolean'
        ]);

        $updated = $this->documentoTipoService->update($documentoTipo, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentoTipo $documentoTipo): JsonResponse
    {
        $this->documentoTipoService->delete($documentoTipo);
        return response()->json(null, 204);
    }
}
