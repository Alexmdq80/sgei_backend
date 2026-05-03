<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Services\AnioService;
use App\Http\Requests\Api\V1\AnioRequest;
use Illuminate\Http\JsonResponse;

class AnioController extends Controller
{
    protected AnioService $anioService;

    public function __construct(AnioService $anioService)
    {
        $this->anioService = $anioService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->anioService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnioRequest $request): JsonResponse
    {
        $anio = $this->anioService->create($request->validated());
        return response()->json($anio, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Anio $anio): JsonResponse
    {
        return response()->json($anio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnioRequest $request, Anio $anio): JsonResponse
    {
        $anio = $this->anioService->update($anio, $request->validated());
        return response()->json($anio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anio $anio): JsonResponse
    {
        $this->anioService->delete($anio);
        return response()->json(null, 204);
    }
}
