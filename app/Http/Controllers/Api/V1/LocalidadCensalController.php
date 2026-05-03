<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LocalidadCensal;
use App\Services\LocalidadCensalService;
use App\Http\Requests\Api\V1\LocalidadCensalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocalidadCensalController extends Controller
{
    public function __construct(
        protected LocalidadCensalService $localidadCensalService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->localidadCensalService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocalidadCensalRequest $request): JsonResponse
    {
        $item = $this->localidadCensalService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->localidadCensalService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocalidadCensalRequest $request, LocalidadCensal $localidadCensal): JsonResponse
    {
        $updated = $this->localidadCensalService->update($localidadCensal, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocalidadCensal $localidadCensal): JsonResponse
    {
        $this->localidadCensalService->delete($localidadCensal);
        return response()->json(null, 204);
    }
}
