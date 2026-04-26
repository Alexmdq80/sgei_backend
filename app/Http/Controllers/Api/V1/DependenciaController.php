<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dependencia;
use App\Services\DependenciaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DependenciaController extends Controller
{
    protected DependenciaService $dependenciaService;

    public function __construct(DependenciaService $dependenciaService)
    {
        $this->dependenciaService = $dependenciaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->dependenciaService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:dependencias,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $dependencia = $this->dependenciaService->create($request->all());
        return response()->json($dependencia, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dependencia $dependencia): JsonResponse
    {
        return response()->json($dependencia);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dependencia $dependencia): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:dependencias,nombre,' . $dependencia->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $dependencia = $this->dependenciaService->update($dependencia, $request->all());
        return response()->json($dependencia);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dependencia $dependencia): JsonResponse
    {
        $this->dependenciaService->delete($dependencia);
        return response()->json(null, 204);
    }
}
