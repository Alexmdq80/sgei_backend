<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Propuesta;
use App\Services\PropuestaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropuestaController extends Controller
{
    public function __construct(
        protected PropuestaService $propuestaService
    ) {}

    /**
     * Display a listing of authorized schools for the user.
     */
    public function getAuthorizedSchools(Request $request): JsonResponse
    {
        $schools = $this->propuestaService->getAuthorizedSchools($request->user());
        return response()->json($schools);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $propuestas = $this->propuestaService->getAllPropuestas($request->user(), $request->all());
        return response()->json($propuestas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'escuela_id' => 'required|exists:escuelas,id',
            'anio_plan_id' => 'required|exists:anio_plan,id',
            'turno_inicio_id' => 'nullable|exists:turnos,id',
            'turno_fin_id' => 'nullable|exists:turnos,id',
            'jornada_id' => 'nullable|exists:jornadas,id',
            'lectivo_id' => 'required|exists:lectivos,id',
        ]);

        $propuesta = $this->propuestaService->createPropuesta($request->user(), $validated);
        return response()->json($propuesta, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $propuesta = $this->propuestaService->getPropuestaById($request->user(), $id);
        return response()->json($propuesta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Propuesta $propuesta): JsonResponse
    {
        $validated = $request->validate([
            'escuela_id' => 'required|exists:escuelas,id',
            'anio_plan_id' => 'required|exists:anio_plan,id',
            'turno_inicio_id' => 'nullable|exists:turnos,id',
            'turno_fin_id' => 'nullable|exists:turnos,id',
            'jornada_id' => 'nullable|exists:jornadas,id',
            'lectivo_id' => 'required|exists:lectivos,id',
        ]);

        $updated = $this->propuestaService->updatePropuesta($request->user(), $propuesta, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Propuesta $propuesta): JsonResponse
    {
        $this->propuestaService->deletePropuesta($request->user(), $propuesta);
        return response()->json(null, 204);
    }
}
