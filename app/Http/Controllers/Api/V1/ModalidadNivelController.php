<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ModalidadNivel;
use App\Services\ModalidadNivelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModalidadNivelController extends Controller
{
    protected ModalidadNivelService $modalidadNivelService;

    public function __construct(ModalidadNivelService $modalidadNivelService)
    {
        $this->modalidadNivelService = $modalidadNivelService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->modalidadNivelService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nivel_id' => 'required|exists:nivels,id',
            'modalidad_id' => 'required|exists:modalidads,id',
            'escuela_tipo_id' => 'nullable|exists:escuela_tipos,id',
        ]);

        // Evitar duplicados
        $validator->after(function ($validator) use ($request) {
            $exists = ModalidadNivel::where('nivel_id', $request->nivel_id)
                ->where('modalidad_id', $request->modalidad_id)
                ->where('escuela_tipo_id', $request->escuela_tipo_id)
                ->exists();
            if ($exists) {
                $validator->errors()->add('nivel_id', 'Esta combinación ya existe.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $combination = $this->modalidadNivelService->create($request->all());
        return response()->json($combination, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModalidadNivel $modalidadNivel): JsonResponse
    {
        return response()->json($modalidadNivel->load(['nivel', 'modalidad', 'escuelaTipo']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModalidadNivel $modalidadNivel): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nivel_id' => 'required|exists:nivels,id',
            'modalidad_id' => 'required|exists:modalidads,id',
            'escuela_tipo_id' => 'nullable|exists:escuela_tipos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $combination = $this->modalidadNivelService->update($modalidadNivel, $request->all());
        return response()->json($combination);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModalidadNivel $modalidadNivel): JsonResponse
    {
        $this->modalidadNivelService->delete($modalidadNivel);
        return response()->json(null, 204);
    }
}
