<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ambito;
use App\Services\AmbitoService;
use App\Http\Requests\Api\V1\AmbitoRequest;
use Illuminate\Http\JsonResponse;

class AmbitoController extends Controller
{
    public function __construct(
        protected AmbitoService $ambitoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->ambitoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AmbitoRequest $request): JsonResponse
    {
        $ambito = $this->ambitoService->create($request->validated());
        return response()->json($ambito, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ambito $ambito): JsonResponse
    {
        return response()->json($ambito);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AmbitoRequest $request, Ambito $ambito): JsonResponse
    {
        $ambito = $this->ambitoService->update($ambito, $request->validated());
        return response()->json($ambito);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ambito $ambito): JsonResponse
    {
        $this->ambitoService->delete($ambito);
        return response()->json(null, 204);
    }
}
