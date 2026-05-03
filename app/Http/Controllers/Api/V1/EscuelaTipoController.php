<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EscuelaTipo;
use App\Services\EscuelaTipoService;
use App\Http\Requests\Api\V1\EscuelaTipoRequest;
use Illuminate\Http\JsonResponse;

class EscuelaTipoController extends Controller
{
    public function __construct(
        protected EscuelaTipoService $escuelaTipoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->escuelaTipoService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EscuelaTipoRequest $request): JsonResponse
    {
        $escuelaTipo = $this->escuelaTipoService->create($request->validated());
        return response()->json($escuelaTipo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EscuelaTipo $escuelaTipo): JsonResponse
    {
        return response()->json($escuelaTipo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EscuelaTipoRequest $request, EscuelaTipo $escuelaTipo): JsonResponse
    {
        $escuelaTipo = $this->escuelaTipoService->update($escuelaTipo, $request->validated());
        return response()->json($escuelaTipo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EscuelaTipo $escuelaTipo): JsonResponse
    {
        $this->escuelaTipoService->delete($escuelaTipo);
        return response()->json(null, 204);
    }
}
