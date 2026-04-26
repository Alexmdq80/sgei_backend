<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EscuelaUbicacion;
use App\Services\EscuelaUbicacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EscuelaUbicacionController extends Controller
{
    protected EscuelaUbicacionService $escuelaUbicacionService;

    public function __construct(EscuelaUbicacionService $escuelaUbicacionService)
    {
        $this->escuelaUbicacionService = $escuelaUbicacionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->escuelaUbicacionService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:escuela_ubicacions,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $escuelaUbicacion = $this->escuelaUbicacionService->create($request->all());
        return response()->json($escuelaUbicacion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EscuelaUbicacion $escuelaUbicacion): JsonResponse
    {
        return response()->json($escuelaUbicacion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EscuelaUbicacion $escuelaUbicacion): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:escuela_ubicacions,nombre,' . $escuelaUbicacion->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $escuelaUbicacion = $this->escuelaUbicacionService->update($escuelaUbicacion, $request->all());
        return response()->json($escuelaUbicacion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EscuelaUbicacion $escuelaUbicacion): JsonResponse
    {
        $this->escuelaUbicacionService->delete($escuelaUbicacion);
        return response()->json(null, 204);
    }
}
