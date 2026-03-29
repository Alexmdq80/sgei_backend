<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        $niveles = \App\Models\Nivel::where('vigente', true)->orderBy('orden')->get(['id', 'nombre']);
        return response()->json($niveles);
    }

    /**
     * Get all school sectors.
     */
    public function sectores(): JsonResponse
    {
        $sectores = \App\Models\Sector::where('vigente', true)->orderBy('orden')->get(['id', 'nombre']);
        return response()->json($sectores);
    }

    /**
     * Request to join a school.
     */
    public function requestJoin(Request $request): JsonResponse
    {
        $request->validate([
            'escuela_id' => 'required|integer|exists:escuelas,id',
            'rol_escolar_id' => 'nullable|integer|exists:roles_escolares,id'
        ]);

        $user = Auth::user();
        
        $rolEscolarId = $request->input('rol_escolar_id', 1);

        $this->escuelaService->requestJoin($user, $request->escuela_id, $rolEscolarId);

        return response()->json([
            'message' => 'Solicitud enviada con éxito. Espere la aprobación del administrador.',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Cancel join request.
     */
    public function cancelJoin(Request $request): JsonResponse
    {
        $user = Auth::user();
        $this->escuelaService->cancelJoin($user, $request->input('escuela_id'));

        return response()->json([
            'message' => 'Solicitud cancelada.',
            'user' => $user->fresh()->load(['escuelaUsuarios.escuela', 'escuelaUsuarios.rolEscolar'])
        ]);
    }
}
