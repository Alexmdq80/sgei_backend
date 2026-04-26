<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EscuelaTipo;
use App\Services\EscuelaTipoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EscuelaTipoController extends Controller
{
    protected EscuelaTipoService $escuelaTipoService;

    public function __construct(EscuelaTipoService $escuelaTipoService)
    {
        $this->escuelaTipoService = $escuelaTipoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->escuelaTipoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:escuela_tipos,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $escuelaTipo = $this->escuelaTipoService->create($request->all());
        return response()->json($escuelaTipo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EscuelaTipo $escuelaTipo): JsonResponse
    {
        return response()->json($escuelaTipo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EscuelaTipo $escuelaTipo): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:escuela_tipos,nombre,' . $escuelaTipo->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $escuelaTipo = $this->escuelaTipoService->update($escuelaTipo, $request->all());
        return response()->json($escuelaTipo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EscuelaTipo $escuelaTipo): JsonResponse
    {
        $this->escuelaTipoService->delete($escuelaTipo);
        return response()->json(null, 204);
    }
}
