<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ModalidadNivel;
use App\Services\ModalidadNivelService;
use App\Http\Requests\Api\V1\ModalidadNivelRequest;
use Illuminate\Http\JsonResponse;

class ModalidadNivelController extends Controller
{
    protected ModalidadNivelService $modalidadNivelService;

    public function __construct(ModalidadNivelService $modalidadNivelService)
    {
        $this->modalidadNivelService = $modalidadNivelService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->modalidadNivelService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModalidadNivelRequest $request): JsonResponse
    {
        $combination = $this->modalidadNivelService->create($request->validated());
        return response()->json($combination, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModalidadNivel $modalidadNivel): JsonResponse
    {
        return response()->json($modalidadNivel->load(['nivel', 'modalidad', 'escuelaTipo']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModalidadNivelRequest $request, ModalidadNivel $modalidadNivel): JsonResponse
    {
        $combination = $this->modalidadNivelService->update($modalidadNivel, $request->validated());
        return response()->json($combination);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModalidadNivel $modalidadNivel): JsonResponse
    {
        $this->modalidadNivelService->delete($modalidadNivel);
        return response()->json(null, 204);
    }
}
