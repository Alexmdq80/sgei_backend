<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CierreCausa;
use App\Services\CierreCausaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CierreCausaController extends Controller
{
    protected CierreCausaService $cierreCausaService;

    public function __construct(CierreCausaService $cierreCausaService)
    {
        $this->cierreCausaService = $cierreCausaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->cierreCausaService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:cierre_causas,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $cierreCausa = $this->cierreCausaService->create($request->all());
        return response()->json($cierreCausa, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CierreCausa $cierreCausa): JsonResponse
    {
        return response()->json($cierreCausa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CierreCausa $cierreCausa): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:cierre_causas,nombre,' . $cierreCausa->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $cierreCausa = $this->cierreCausaService->update($cierreCausa, $request->all());
        return response()->json($cierreCausa);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CierreCausa $cierreCausa): JsonResponse
    {
        $this->cierreCausaService->delete($cierreCausa);
        return response()->json(null, 204);
    }
}
