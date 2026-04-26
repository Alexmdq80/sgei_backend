<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ambito;
use App\Services\AmbitoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AmbitoController extends Controller
{
    protected AmbitoService $ambitoService;

    public function __construct(AmbitoService $ambitoService)
    {
        $this->ambitoService = $ambitoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->ambitoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:ambitos,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $ambito = $this->ambitoService->create($request->all());
        return response()->json($ambito, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ambito $ambito): JsonResponse
    {
        return response()->json($ambito);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ambito $ambito): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:ambitos,nombre,' . $ambito->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $ambito = $this->ambitoService->update($ambito, $request->all());
        return response()->json($ambito);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ambito $ambito): JsonResponse
    {
        $this->ambitoService->delete($ambito);
        return response()->json(null, 204);
    }
}
