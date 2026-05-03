<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Modalidad;
use App\Services\ModalidadService;
use App\Http\Requests\Api\V1\ModalidadRequest;
use Illuminate\Http\JsonResponse;

class ModalidadController extends Controller
{
    public function __construct(
        protected ModalidadService $modalidadService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->modalidadService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModalidadRequest $request): JsonResponse
    {
        $modalidad = $this->modalidadService->create($request->validated());
        return response()->json($modalidad, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Modalidad $modalidad): JsonResponse
    {
        return response()->json($modalidad);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModalidadRequest $request, Modalidad $modalidad): JsonResponse
    {
        $modalidad = $this->modalidadService->update($modalidad, $request->validated());
        return response()->json($modalidad);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modalidad $modalidad): JsonResponse
    {
        $this->modalidadService->delete($modalidad);
        return response()->json(null, 204);
    }
}
