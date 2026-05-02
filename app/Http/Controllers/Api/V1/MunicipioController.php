<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use App\Services\MunicipioService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MunicipioController extends Controller
{
    public function __construct(
        protected MunicipioService $municipioService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->municipioService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provincia_id' => 'required|exists:provincias,id',
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|unique:municipios,id_georef'
        ], [
            'id_georef.unique' => 'El ID Georef ya está asignado a otro municipio.'
        ]);

        $item = $this->municipioService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->municipioService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Municipio $municipio): JsonResponse
    {
        $validated = $request->validate([
            'provincia_id' => 'required|exists:provincias,id',
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|integer|unique:municipios,id_georef,' . $municipio->id
        ], [
            'id_georef.unique' => 'El ID Georef ya está asignado a otro municipio.'
        ]);

        $updated = $this->municipioService->update($municipio, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Municipio $municipio): JsonResponse
    {
        $this->municipioService->delete($municipio);
        return response()->json(null, 204);
    }
}
