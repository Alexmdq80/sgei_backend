<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provincia;
use App\Services\ProvinciaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvinciaController extends Controller
{
    public function __construct(
        protected ProvinciaService $provinciaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->provinciaService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nacion_id' => 'required|exists:nacions,id',
            'nombre' => 'required|string|max:255|unique:provincias,nombre',
            'id_georef' => 'nullable|unique:provincias,id_georef',
            'iso_id' => 'nullable|string|max:10'
        ], [
            'id_georef.unique' => 'El ID Georef ingresado ya está asignado a otra provincia.',
            'nombre.unique' => 'Ya existe una provincia con este nombre.'
        ]);

        $item = $this->provinciaService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->provinciaService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Provincia $provincia): JsonResponse
    {
        $validated = $request->validate([
            'nacion_id' => 'required|exists:nacions,id',
            'nombre' => 'required|string|max:255|unique:provincias,nombre,' . $provincia->id,
            'id_georef' => 'nullable|integer|unique:provincias,id_georef,' . $provincia->id,
            'iso_id' => 'nullable|string|max:10'
        ], [
            'id_georef.unique' => 'El ID Georef ingresado ya está asignado a otra provincia.',
            'nombre.unique' => 'Ya existe una provincia con este nombre.'
        ]);

        $updated = $this->provinciaService->update($provincia, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provincia $provincia): JsonResponse
    {
        $this->provinciaService->delete($provincia);
        return response()->json(null, 204);
    }
}
