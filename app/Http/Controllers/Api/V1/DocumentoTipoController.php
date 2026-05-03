<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentoTipo;
use App\Services\DocumentoTipoService;
use App\Http\Requests\Api\V1\DocumentoTipoRequest;
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
        return response()->json($this->documentoTipoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentoTipoRequest $request): JsonResponse
    {
        $item = $this->documentoTipoService->create($request->validated());
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
    public function update(DocumentoTipoRequest $request, DocumentoTipo $documentoTipo): JsonResponse
    {
        $updated = $this->documentoTipoService->update($documentoTipo, $request->validated());
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
