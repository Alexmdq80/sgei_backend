<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CargoService;
use App\Http\Requests\Api\V1\CargoRequest;
use App\Models\Cargo;
use Illuminate\Http\JsonResponse;

class CargoController extends Controller
{
    public function __construct(
        protected CargoService $cargoService
    ) {}

    /**
     * Display a listing of active cargos.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->cargoService->listActive());
    }

    /**
     * Store a newly created cargo in storage.
     */
    public function store(CargoRequest $request): JsonResponse
    {
        $cargo = $this->cargoService->store($request->validated());
        return response()->json($cargo, 201);
    }

    /**
     * Display the specified cargo.
     */
    public function show(Cargo $cargo): JsonResponse
    {
        return response()->json($cargo);
    }

    /**
     * Update the specified cargo in storage.
     */
    public function update(CargoRequest $request, Cargo $cargo): JsonResponse
    {
        $updatedCargo = $this->cargoService->update($cargo, $request->validated());
        return response()->json($updatedCargo);
    }

    /**
     * Remove the specified cargo from storage.
     */
    public function destroy(Cargo $cargo): JsonResponse
    {
        $this->cargoService->delete($cargo);
        return response()->json(null, 204);
    }
}
