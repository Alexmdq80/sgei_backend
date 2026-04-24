<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lectivo;
use App\Services\LectivoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LectivoController extends Controller
{
    public function __construct(
        protected LectivoService $lectivoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $lectivos = $this->lectivoService->getAll($request->all());
        return response()->json($lectivos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'anio' => 'required|integer|min:2000|max:2100',
            'orden' => 'nullable|integer',
            'vigente' => 'boolean',
            'cerrado' => 'boolean',
        ]);

        $lectivo = $this->lectivoService->create($validated);
        return response()->json($lectivo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lectivo $lectivo): JsonResponse
    {
        return response()->json($lectivo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lectivo $lectivo): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'anio' => 'required|integer|min:2000|max:2100',
            'orden' => 'nullable|integer',
            'vigente' => 'boolean',
            'cerrado' => 'boolean',
        ]);

        $updated = $this->lectivoService->update($lectivo, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lectivo $lectivo): JsonResponse
    {
        $this->lectivoService->delete($lectivo);
        return response()->json(null, 204);
    }
}
