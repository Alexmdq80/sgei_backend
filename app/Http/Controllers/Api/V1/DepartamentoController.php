<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Services\DepartamentoService;
use App\Http\Requests\Api\V1\DepartamentoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartamentoController extends Controller
{
    public function __construct(
        protected DepartamentoService $departamentoService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);

        $user = $request->user();
        $filters = [];
        if ($request->has('region_id')) {
            $filters['region_id'] = $request->query('region_id');
        }
        if ($request->has('provincia_id')) {
            $filters['provincia_id'] = $request->query('provincia_id');
        }
        return response()->json($this->departamentoService->getAll($search, (int) $perPage, $filters));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartamentoRequest $request): JsonResponse
    {
        $item = $this->departamentoService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->departamentoService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartamentoRequest $request, Departamento $departamento): JsonResponse
    {
        $updated = $this->departamentoService->update($departamento, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departamento $departamento): JsonResponse
    {
        $this->departamentoService->delete($departamento);
        return response()->json(null, 204);
    }
}
