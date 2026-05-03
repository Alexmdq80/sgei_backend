<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentoSituacion;
use App\Services\DocumentoSituacionService;
use App\Http\Requests\Api\V1\DocumentoSituacionRequest;
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
    public function store(DocumentoSituacionRequest $request): JsonResponse
    {
        $item = $this->documentoSituacionService->create($request->validated());
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
    public function update(DocumentoSituacionRequest $request, DocumentoSituacion $documentoSituacion): JsonResponse
    {
        $updated = $this->documentoSituacionService->update($documentoSituacion, $request->validated());
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
