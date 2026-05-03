<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sexo;
use App\Services\SexoService;
use App\Http\Requests\Api\V1\SexoRequest;
use Illuminate\Http\JsonResponse;

class SexoController extends Controller
{
    public function __construct(
        protected SexoService $sexoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->sexoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SexoRequest $request): JsonResponse
    {
        $item = $this->sexoService->create($request->validated());
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->sexoService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SexoRequest $request, Sexo $sexo): JsonResponse
    {
        $updated = $this->sexoService->update($sexo, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sexo $sexo): JsonResponse
    {
        $this->sexoService->delete($sexo);
        return response()->json(null, 204);
    }
}
