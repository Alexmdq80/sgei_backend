<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Cupof;
use App\Services\CupofService;
use App\Http\Requests\Api\V1\CupofRequest;
use App\Http\Requests\Api\V1\CupofAssignRequest;
use App\Http\Requests\Api\V1\CupofReleaseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CupofController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected CupofService $cupofService
    ) {}

    /**
     * List all CUPOFs with filtering options.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Cupof::class);

        $cupofs = $this->cupofService->getAllCupofs($request->all());
        return response()->json($cupofs);
    }

    /**
     * Create a new CUPOF slot.
     */
    public function store(CupofRequest $request): JsonResponse
    {
        $this->authorize('create', Cupof::class);

        $cupof = $this->cupofService->createCupof($request->validated());
        return response()->json($cupof, 201);
    }

    /**
     * Assign a persona to a CUPOF.
     */
    public function assign(CupofAssignRequest $request, Cupof $cupof): JsonResponse
    {
        $this->authorize('assign', $cupof);

        $validated = $request->validated();
        $persona = Persona::findOrFail($validated['persona_id']);
        $movimiento = $this->cupofService->assignPersona($cupof, $persona, $validated);

        return response()->json([
            'message' => 'Persona asignada exitosamente al CUPOF',
            'movimiento' => $movimiento
        ]);
    }

    /**
     * Release a CUPOF slot.
     */
    public function release(CupofReleaseRequest $request, Cupof $cupof): JsonResponse
    {
        $this->authorize('release', $cupof);

        $this->cupofService->releaseCupof($cupof, $request->validated()['motivo_baja'] ?? null);

        return response()->json([
            'message' => 'Puesto liberado exitosamente'
        ]);
    }
}
