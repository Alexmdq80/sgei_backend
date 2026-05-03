<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Localidad;
use App\Services\LocalidadService;
use App\Http\Requests\Api\V1\LocalidadRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocalidadController extends Controller
{
    public function __construct(
        protected LocalidadService $localidadService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->localidadService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocalidadRequest $request): JsonResponse
    {
        $item = $this->localidadService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->localidadService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocalidadRequest $request, Localidad $localidad): JsonResponse
    {
        $updated = $this->localidadService->update($localidad, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Localidad $localidad): JsonResponse
    {
        $this->localidadService->delete($localidad);
        return response()->json(null, 204);
    }
}
