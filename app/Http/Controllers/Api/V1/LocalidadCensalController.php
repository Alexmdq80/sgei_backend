<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LocalidadCensal;
use App\Services\LocalidadCensalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocalidadCensalController extends Controller
{
    public function __construct(
        protected LocalidadCensalService $localidadCensalService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->localidadCensalService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|string|max:255',
            'georef_fuente_id' => 'nullable|exists:georef_fuentes,id',
            'georef_categoria_id' => 'nullable|exists:georef_categorias,id',
            'georef_funcion_id' => 'nullable|exists:georef_funcions,id',
            'centroide_lat' => 'nullable|numeric',
            'centroide_lon' => 'nullable|numeric'
        ]);

        $item = $this->localidadCensalService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->localidadCensalService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LocalidadCensal $localidadCensal): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'id_georef' => 'nullable|string|max:255',
            'georef_fuente_id' => 'nullable|exists:georef_fuentes,id',
            'georef_categoria_id' => 'nullable|exists:georef_categorias,id',
            'georef_funcion_id' => 'nullable|exists:georef_funcions,id',
            'centroide_lat' => 'nullable|numeric',
            'centroide_lon' => 'nullable|numeric'
        ]);

        $updated = $this->localidadCensalService->update($localidadCensal, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocalidadCensal $localidadCensal): JsonResponse
    {
        $this->localidadCensalService->delete($localidadCensal);
        return response()->json(null, 204);
    }
}
