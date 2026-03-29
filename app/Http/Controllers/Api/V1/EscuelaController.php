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
        $term = $request->query('search');
        $filters = $request->only(['provincia_id', 'departamento_id', 'localidad_id']);
        
        $escuelas = $this->escuelaService->search($term, $filters);

        return response()->json($escuelas);
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
    public function cancelJoin(): JsonResponse
    {
        $user = Auth::user();
        $this->escuelaService->cancelJoin($user);

        return response()->json([
            'message' => 'Solicitud cancelada.',
            'user' => $user->fresh()
        ]);
    }
}
