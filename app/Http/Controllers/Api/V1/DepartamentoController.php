<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Services\DepartamentoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartamentoController extends Controller
{
    public function __construct(
        protected DepartamentoService $departamentoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->departamentoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provincia_id' => 'required|exists:provincias,id',
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|integer'
        ]);

        $item = $this->departamentoService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->departamentoService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Departamento $departamento): JsonResponse
    {
        $validated = $request->validate([
            'provincia_id' => 'required|exists:provincias,id',
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|integer'
        ]);

        $updated = $this->departamentoService->update($departamento, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departamento $departamento): JsonResponse
    {
        $this->departamentoService->delete($departamento);
        return response()->json(null, 204);
    }
}
