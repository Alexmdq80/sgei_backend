<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CierreCausa;
use App\Services\CierreCausaService;
use App\Http\Requests\Api\V1\CierreCausaRequest;
use Illuminate\Http\JsonResponse;

class CierreCausaController extends Controller
{
    public function __construct(
        protected CierreCausaService $cierreCausaService
    ) {}

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
    public function store(CierreCausaRequest $request): JsonResponse
    {
        $cierreCausa = $this->cierreCausaService->create($request->validated());
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
    public function update(CierreCausaRequest $request, CierreCausa $cierreCausa): JsonResponse
    {
        $cierreCausa = $this->cierreCausaService->update($cierreCausa, $request->validated());
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
