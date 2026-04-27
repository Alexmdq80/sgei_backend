<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentoSituacion;
use App\Services\DocumentoSituacionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DocumentoSituacionController extends Controller
{
    public function __construct(
        protected DocumentoSituacionService $documentoSituacionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->documentoSituacionService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:documento_situacions,nombre',
            'vigente' => 'boolean'
        ]);

        $item = $this->documentoSituacionService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->documentoSituacionService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentoSituacion $documentoSituacion): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:documento_situacions,nombre,' . $documentoSituacion->id,
            'vigente' => 'boolean'
        ]);

        $updated = $this->documentoSituacionService->update($documentoSituacion, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentoSituacion $documentoSituacion): JsonResponse
    {
        $this->documentoSituacionService->delete($documentoSituacion);
        return response()->json(null, 204);
    }
}
