<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Condicion;
use App\Services\CondicionService;
use App\Http\Requests\Api\V1\CondicionRequest;
use Illuminate\Http\JsonResponse;

class CondicionController extends Controller
{
    public function __construct(
        protected CondicionService $condicionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->condicionService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CondicionRequest $request): JsonResponse
    {
        $condicion = $this->condicionService->create($request->validated());
        return response()->json($condicion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Condicion $condicion): JsonResponse
    {
        return response()->json($condicion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CondicionRequest $request, Condicion $condicion): JsonResponse
    {
        $condicion = $this->condicionService->update($condicion, $request->validated());
        return response()->json($condicion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condicion $condicion): JsonResponse
    {
        $this->condicionService->delete($condicion);
        return response()->json(null, 204);
    }
}
