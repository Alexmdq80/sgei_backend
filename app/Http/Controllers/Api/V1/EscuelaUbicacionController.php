<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EscuelaUbicacion;
use App\Services\EscuelaUbicacionService;
use App\Http\Requests\Api\V1\EscuelaUbicacionRequest;
use Illuminate\Http\JsonResponse;

class EscuelaUbicacionController extends Controller
{
    public function __construct(
        protected EscuelaUbicacionService $escuelaUbicacionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->escuelaUbicacionService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EscuelaUbicacionRequest $request): JsonResponse
    {
        $escuelaUbicacion = $this->escuelaUbicacionService->create($request->validated());
        return response()->json($escuelaUbicacion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EscuelaUbicacion $escuelaUbicacion): JsonResponse
    {
        return response()->json($escuelaUbicacion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EscuelaUbicacionRequest $request, EscuelaUbicacion $escuelaUbicacion): JsonResponse
    {
        $escuelaUbicacion = $this->escuelaUbicacionService->update($escuelaUbicacion, $request->validated());
        return response()->json($escuelaUbicacion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EscuelaUbicacion $escuelaUbicacion): JsonResponse
    {
        $this->escuelaUbicacionService->delete($escuelaUbicacion);
        return response()->json(null, 204);
    }
}
