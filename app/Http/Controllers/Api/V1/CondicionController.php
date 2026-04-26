<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Condicion;
use App\Services\CondicionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CondicionController extends Controller
{
    protected CondicionService $condicionService;

    public function __construct(CondicionService $condicionService)
    {
        $this->condicionService = $condicionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->condicionService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:condicions,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $condicion = $this->condicionService->create($request->all());
        return response()->json($condicion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Condicion $condicion): JsonResponse
    {
        return response()->json($condicion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Condicion $condicion): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:condicions,nombre,' . $condicion->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $condicion = $this->condicionService->update($condicion, $request->all());
        return response()->json($condicion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condicion $condicion): JsonResponse
    {
        $this->condicionService->delete($condicion);
        return response()->json(null, 204);
    }
}
