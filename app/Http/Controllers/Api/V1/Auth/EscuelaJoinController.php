<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EscuelaJoinController extends Controller
{
    protected EscuelaService $escuelaService;

    public function __construct(EscuelaService $escuelaService)
    {
        $this->escuelaService = $escuelaService;
    }

    /**
     * User requests to join a school.
     */
    public function join(Request $request): JsonResponse
    {
        $request->validate([
            'escuela_id' => 'required|exists:escuelas,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            $this->escuelaService->joinSchool(
                Auth::user(),
                $request->escuela_id,
                $request->role_id
            );

            return response()->json([
                'message' => 'Solicitud enviada con éxito. Espere la aprobación del administrador.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 400
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * User cancels their own join request.
     */
    public function cancelJoin(Request $request): JsonResponse
    {
        $request->validate([
            'escuela_id' => 'required|exists:escuelas,id'
        ]);

        try {
            $this->escuelaService->cancelJoinRequest(
                Auth::user(),
                $request->escuela_id
            );

            return response()->json([
                'message' => 'Solicitud cancelada.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 400
            ], $e->getCode() ?: 400);
        }
    }
}
