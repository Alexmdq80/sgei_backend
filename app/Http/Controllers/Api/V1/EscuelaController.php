<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EscuelaController extends Controller
{
    protected EscuelaService $escuelaService;

    public function __construct(EscuelaService $escuelaService)
    {
        $this->escuelaService = $escuelaService;
    }

    /**
     * Search schools.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $term = $request->query('search');
            $filters = $request->only(['provincia_id', 'departamento_id', 'localidad_id', 'nivel_id', 'sector_id']);
            
            $escuelas = $this->escuelaService->search($term, $filters);

            return response()->json($escuelas);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al buscar escuelas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all school levels.
     */
    public function niveles(): JsonResponse
    {
        $niveles = \App\Models\Nivel::where('vigente', true)->orderBy('id')->get(['id', 'nombre']);
        return response()->json($niveles);
    }

    /**
     * Get all school sectors.
     */
    public function sectores(): JsonResponse
    {
        $sectores = \App\Models\Sector::where('vigente', true)->orderBy('nombre')->get(['id', 'nombre']);
        return response()->json($sectores);
    }
}
