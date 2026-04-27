<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sexo;
use App\Services\SexoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SexoController extends Controller
{
    public function __construct(
        protected SexoService $sexoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->sexoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:sexos,nombre',
            'letra' => 'required|string|max:1',
            'orden' => 'nullable|integer|min:0|max:255',
            'vigente' => 'boolean'
        ]);

        $item = $this->sexoService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->sexoService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sexo $sexo): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:sexos,nombre,' . $sexo->id,
            'letra' => 'required|string|max:1',
            'orden' => 'nullable|integer|min:0|max:255',
            'vigente' => 'boolean'
        ]);

        $updated = $this->sexoService->update($sexo, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sexo $sexo): JsonResponse
    {
        $this->sexoService->delete($sexo);
        return response()->json(null, 204);
    }
}
