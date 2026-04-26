<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use App\Services\NivelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NivelController extends Controller
{
    protected NivelService $nivelService;

    public function __construct(NivelService $nivelService)
    {
        $this->nivelService = $nivelService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->nivelService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:nivels,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $nivel = $this->nivelService->create($request->all());
        return response()->json($nivel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Nivel $nivel): JsonResponse
    {
        return response()->json($nivel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nivel $nivel): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:nivels,nombre,' . $nivel->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $nivel = $this->nivelService->update($nivel, $request->all());
        return response()->json($nivel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nivel $nivel): JsonResponse
    {
        $this->nivelService->delete($nivel);
        return response()->json(null, 204);
    }
}
