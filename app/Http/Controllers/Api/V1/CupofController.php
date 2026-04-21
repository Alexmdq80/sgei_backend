<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Cupof;
use App\Services\CupofService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CupofController extends Controller
{
    public function __construct(
        protected CupofService $cupofService
    ) {}

    /**
     * List all CUPOFs with filtering options.
     */
    public function index(Request $request): JsonResponse
    {
        $cupofs = $this->cupofService->getAllCupofs($request->all());
        return response()->json($cupofs);
    }

    /**
     * Create a new CUPOF slot.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_cupof' => 'required|string|unique:cupofs,codigo_cupof',
            'escuela_id' => 'required|exists:escuelas,id',
            'asignatura_id' => 'nullable|exists:asignaturas,id',
            'nombre_cargo' => 'nullable|string|max:255',
            'escalafon' => 'required|in:docente,auxiliar,administrativo',
            'tipo_puesto' => 'required|in:cargo,horas_catedra,modulos',
            'cantidad' => 'integer|min:1'
        ]);

        $cupof = $this->cupofService->createCupof($validated);
        return response()->json($cupof, 201);
    }

    /**
     * Assign a persona to a CUPOF.
     */
    public function assign(Request $request, Cupof $cupof): JsonResponse
    {
        $validated = $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'situacion_revista' => 'required|in:titular,provisional,suplente',
            'fecha_inicio' => 'required|date',
            'resolucion' => 'nullable|string'
        ]);

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
    public function release(Request $request, Cupof $cupof): JsonResponse
    {
        $validated = $request->validate([
            'motivo_baja' => 'nullable|string|max:255'
        ]);

        $this->cupofService->releaseCupof($cupof, $validated['motivo_baja'] ?? null);

        return response()->json([
            'message' => 'Puesto liberado exitosamente'
        ]);
    }
}
