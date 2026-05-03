<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Continente;
use App\Services\ContinenteService;
use App\Http\Requests\Api\V1\ContinenteRequest;
use Illuminate\Http\JsonResponse;

class ContinenteController extends Controller
{
    public function __construct(
        protected ContinenteService $continenteService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->continenteService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContinenteRequest $request): JsonResponse
    {
        $item = $this->continenteService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->continenteService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContinenteRequest $request, Continente $continente): JsonResponse
    {
        $updated = $this->continenteService->update($continente, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Continente $continente): JsonResponse
    {
        $this->continenteService->delete($continente);
        return response()->json(null, 204);
    }
}
