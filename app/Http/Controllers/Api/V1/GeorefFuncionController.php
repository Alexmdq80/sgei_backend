<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GeorefFuncion;
use App\Services\GeorefFuncionService;
use App\Http\Requests\Api\V1\GeorefFuncionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeorefFuncionController extends Controller
{
    public function __construct(
        protected GeorefFuncionService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 15);
        
        return response()->json($this->service->getAll($search, $perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeorefFuncionRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeorefFuncionRequest $request, GeorefFuncion $georefFuncion): JsonResponse
    {
        $updated = $this->service->update($georefFuncion, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeorefFuncion $georefFuncion): JsonResponse
    {
        $this->service->delete($georefFuncion);
        return response()->json(null, 204);
    }
}
