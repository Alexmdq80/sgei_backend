<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Services\RegionService;
use App\Http\Requests\Api\V1\RegionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    public function __construct(
        protected RegionService $regionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->regionService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegionRequest $request): JsonResponse
    {
        $item = $this->regionService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->regionService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegionRequest $request, Region $regione): JsonResponse
    {
        $updated = $this->regionService->update($regione, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $regione): JsonResponse
    {
        $this->regionService->delete($regione);
        return response()->json(null, 204);
    }
}
