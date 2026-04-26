<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anio;
use App\Services\AnioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnioController extends Controller
{
    protected AnioService $anioService;

    public function __construct(AnioService $anioService)
    {
        $this->anioService = $anioService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->anioService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'nombre_completo' => 'nullable|string|max:255',
            'anio_absoluto' => 'nullable|integer',
            'anio_relativo' => 'nullable|integer',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $anio = $this->anioService->create($request->all());
        return response()->json($anio, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Anio $anio): JsonResponse
    {
        return response()->json($anio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anio $anio): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'nombre_completo' => 'nullable|string|max:255',
            'anio_absoluto' => 'nullable|integer',
            'anio_relativo' => 'nullable|integer',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $anio = $this->anioService->update($anio, $request->all());
        return response()->json($anio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anio $anio): JsonResponse
    {
        $this->anioService->delete($anio);
        return response()->json(null, 204);
    }
}
