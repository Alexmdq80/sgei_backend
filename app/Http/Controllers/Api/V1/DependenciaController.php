<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dependencia;
use App\Services\DependenciaService;
use App\Http\Requests\Api\V1\DependenciaRequest;
use Illuminate\Http\JsonResponse;

class DependenciaController extends Controller
{
    public function __construct(
        protected DependenciaService $dependenciaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->dependenciaService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DependenciaRequest $request): JsonResponse
    {
        $dependencia = $this->dependenciaService->create($request->validated());
        return response()->json($dependencia, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dependencia $dependencia): JsonResponse
    {
        return response()->json($dependencia);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DependenciaRequest $request, Dependencia $dependencia): JsonResponse
    {
        $dependencia = $this->dependenciaService->update($dependencia, $request->validated());
        return response()->json($dependencia);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dependencia $dependencia): JsonResponse
    {
        $this->dependenciaService->delete($dependencia);
        return response()->json(null, 204);
    }
}
