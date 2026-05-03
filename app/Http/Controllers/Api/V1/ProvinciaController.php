<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provincia;
use App\Services\ProvinciaService;
use App\Http\Requests\Api\V1\ProvinciaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvinciaController extends Controller
{
    public function __construct(
        protected ProvinciaService $provinciaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->provinciaService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProvinciaRequest $request): JsonResponse
    {
        $item = $this->provinciaService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->provinciaService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProvinciaRequest $request, Provincia $provincia): JsonResponse
    {
        $updated = $this->provinciaService->update($provincia, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provincia $provincia): JsonResponse
    {
        $this->provinciaService->delete($provincia);
        return response()->json(null, 204);
    }
}
