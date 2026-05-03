<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use App\Services\NivelService;
use App\Http\Requests\Api\V1\NivelRequest;
use Illuminate\Http\JsonResponse;

class NivelController extends Controller
{
    public function __construct(
        protected NivelService $nivelService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->nivelService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NivelRequest $request): JsonResponse
    {
        $nivel = $this->nivelService->create($request->validated());
        return response()->json($nivel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Nivel $nivel): JsonResponse
    {
        return response()->json($nivel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NivelRequest $request, Nivel $nivel): JsonResponse
    {
        $nivel = $this->nivelService->update($nivel, $request->validated());
        return response()->json($nivel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nivel $nivel): JsonResponse
    {
        $this->nivelService->delete($nivel);
        return response()->json(null, 204);
    }
}
