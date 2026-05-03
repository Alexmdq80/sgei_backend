<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use App\Services\OfertaService;
use App\Http\Requests\Api\V1\OfertaRequest;
use Illuminate\Http\JsonResponse;

class OfertaController extends Controller
{
    public function __construct(
        protected OfertaService $ofertaService
    ) {}

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
    public function store(OfertaRequest $request): JsonResponse
    {
        $oferta = $this->ofertaService->create($request->validated());
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
    public function update(OfertaRequest $request, Oferta $oferta): JsonResponse
    {
        $oferta = $this->ofertaService->update($oferta, $request->validated());
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
