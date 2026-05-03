<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lectivo;
use App\Services\LectivoService;
use App\Http\Requests\Api\V1\LectivoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LectivoController extends Controller
{
    public function __construct(
        protected LectivoService $lectivoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $lectivos = $this->lectivoService->getAll($request->all());
        return response()->json($lectivos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LectivoRequest $request): JsonResponse
    {
        $lectivo = $this->lectivoService->create($request->validated());
        return response()->json($lectivo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lectivo $lectivo): JsonResponse
    {
        return response()->json($lectivo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LectivoRequest $request, Lectivo $lectivo): JsonResponse
    {
        $updated = $this->lectivoService->update($lectivo, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lectivo $lectivo): JsonResponse
    {
        $this->lectivoService->delete($lectivo);
        return response()->json(null, 204);
    }
}
