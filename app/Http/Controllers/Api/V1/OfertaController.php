<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use App\Services\OfertaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfertaController extends Controller
{
    protected OfertaService $ofertaService;

    public function __construct(OfertaService $ofertaService)
    {
        $this->ofertaService = $ofertaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->ofertaService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200|unique:ofertas,nombre',
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $oferta = $this->ofertaService->create($request->all());
        return response()->json($oferta, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Oferta $oferta): JsonResponse
    {
        return response()->json($oferta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Oferta $oferta): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200|unique:ofertas,nombre,' . $oferta->id,
            'vigente' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $oferta = $this->ofertaService->update($oferta, $request->all());
        return response()->json($oferta);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Oferta $oferta): JsonResponse
    {
        $this->ofertaService->delete($oferta);
        return response()->json(null, 204);
    }
}
