<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use App\Http\Resources\EscuelaUsuarioResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EscuelaUsuarioController extends Controller
{
    protected EscuelaService $escuelaService;

    public function __construct(EscuelaService $escuelaService)
    {
        $this->escuelaService = $escuelaService;
    }

    /**
     * List pending school join requests.
     */
    public function indexPending(Request $request)
    {
        $filters = $request->only(['escuela_id', 'search', 'per_page']);
        $pending = $this->escuelaService->getPendingRequests($filters);

        return EscuelaUsuarioResource::collection($pending);
    }

    /**
     * Approve a join request.
     */
    public function approve(string $id): JsonResponse
    {
        try {
            $updated = $this->escuelaService->approveJoin($id);
            
            return response()->json([
                'message' => 'Solicitud aprobada con éxito.',
                'data' => new EscuelaUsuarioResource($updated)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 400
            ], 400);
        }
    }

    /**
     * Reject a join request.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'motivo' => 'nullable|string|max:255'
        ]);

        try {
            $this->escuelaService->rejectJoin($id, $request->motivo);
            
            return response()->json([
                'message' => 'Solicitud rechazada y eliminada.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 400
            ], 400);
        }
    }
}
