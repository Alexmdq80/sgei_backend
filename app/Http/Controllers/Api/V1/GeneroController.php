<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use App\Services\GeneroService;
use Illuminate\Http\Request;
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:generos,nombre',
            'orden' => 'nullable|integer|min:0|max:255',
            'vigente' => 'boolean'
        ]);

        $item = $this->generoService->create($validated);
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
    public function update(Request $request, Genero $genero): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:generos,nombre,' . $genero->id,
            'orden' => 'nullable|integer|min:0|max:255',
            'vigente' => 'boolean'
        ]);

        $updated = $this->generoService->update($genero, $validated);
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
