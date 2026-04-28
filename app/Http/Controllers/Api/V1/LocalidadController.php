<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Localidad;
use App\Services\LocalidadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocalidadController extends Controller
{
    public function __construct(
        protected LocalidadService $localidadService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->localidadService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|integer'
        ]);

        $item = $this->localidadService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->localidadService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Localidad $localidad): JsonResponse
    {
        $validated = $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|integer'
        ]);

        $updated = $this->localidadService->update($localidad, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Localidad $localidad): JsonResponse
    {
        $this->localidadService->delete($localidad);
        return response()->json(null, 204);
    }
}
