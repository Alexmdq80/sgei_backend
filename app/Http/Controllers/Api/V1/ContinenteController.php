<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Continente;
use App\Services\ContinenteService;
use Illuminate\Http\Request;
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:continentes,nombre',
            'vigente' => 'boolean'
        ]);

        $item = $this->continenteService->create($validated);
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
    public function update(Request $request, Continente $continente): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:continentes,nombre,' . $continente->id,
            'vigente' => 'boolean'
        ]);

        $updated = $this->continenteService->update($continente, $validated);
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
