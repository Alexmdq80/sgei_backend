<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Propuesta;
use App\Services\PropuestaService;
use App\Http\Requests\Api\V1\PropuestaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropuestaController extends Controller
{
    public function __construct(
        protected PropuestaService $propuestaService
    ) {}

    /**
     * Display a listing of authorized schools for the user.
     */
    public function getAuthorizedSchools(Request $request): JsonResponse
    {
        $schools = $this->propuestaService->getAuthorizedSchools($request->user());
        return response()->json($schools);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $propuestas = $this->propuestaService->getAllPropuestas($request->user(), $request->all());
        return response()->json($propuestas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropuestaRequest $request): JsonResponse
    {
        $propuesta = $this->propuestaService->createPropuesta($request->user(), $request->validated());
        return response()->json($propuesta, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $propuesta = $this->propuestaService->getPropuestaById($request->user(), $id);
        return response()->json($propuesta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropuestaRequest $request, Propuesta $propuesta): JsonResponse
    {
        $updated = $this->propuestaService->updatePropuesta($request->user(), $propuesta, $request->validated());
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Propuesta $propuesta): JsonResponse
    {
        $this->propuestaService->deletePropuesta($request->user(), $propuesta);
        return response()->json(null, 204);
    }
}
