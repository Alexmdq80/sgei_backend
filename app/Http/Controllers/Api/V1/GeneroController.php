<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use App\Services\GeneroService;
use App\Http\Requests\Api\V1\GeneroRequest;
use Illuminate\Http\JsonResponse;

class GeneroController extends Controller
{
    public function __construct(
        protected GeneroService $generoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->generoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeneroRequest $request): JsonResponse
    {
        $item = $this->generoService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->generoService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeneroRequest $request, Genero $genero): JsonResponse
    {
        $updated = $this->generoService->update($genero, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genero $genero): JsonResponse
    {
        $this->generoService->delete($genero);
        return response()->json(null, 204);
    }
}
