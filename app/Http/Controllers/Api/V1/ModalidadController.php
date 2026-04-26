<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Modalidad;
use App\Services\ModalidadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModalidadController extends Controller
{
    protected ModalidadService $modalidadService;

    public function __construct(ModalidadService $modalidadService)
    {
        $this->modalidadService = $modalidadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->modalidadService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:modalidads,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $modalidad = $this->modalidadService->create($request->all());
        return response()->json($modalidad, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Modalidad $modalidad): JsonResponse
    {
        return response()->json($modalidad);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Modalidad $modalidad): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:modalidads,nombre,' . $modalidad->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $modalidad = $this->modalidadService->update($modalidad, $request->all());
        return response()->json($modalidad);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modalidad $modalidad): JsonResponse
    {
        $this->modalidadService->delete($modalidad);
        return response()->json(null, 204);
    }
}
