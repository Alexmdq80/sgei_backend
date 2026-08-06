<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escuela;
use App\Services\EscuelaService;
use App\Http\Requests\Api\V1\Admin\EscuelaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EscuelaController extends Controller
{
    protected EscuelaService $escuelaService;

    public function __construct(EscuelaService $escuelaService)
    {
        $this->escuelaService = $escuelaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $departamentoId = $request->query('localidad_departamento_id');
        $nivelId = $request->query('nivel_id');
        $sectorId = $request->query('sector_id');
        $provinciaId = $request->query('provincia_id');
        $regionId = $request->query('region_id');
        $perPage = $request->query('per_page', 20);
        return response()->json($this->escuelaService->getAllAdmin(
            $search, 
            $departamentoId, 
            $perPage, 
            $nivelId, 
            $sectorId,
            $provinciaId,
            $regionId
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EscuelaRequest $request): JsonResponse
    {
        $this->authorize('create', Escuela::class);
        $dto = \App\DTOs\Escuela\CreateEscuelaDTO::fromRequest($request);
        $escuela = $this->escuelaService->create($dto);
        return response()->json($escuela, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Escuela $escuela): JsonResponse
    {
        $this->authorize('view', $escuela);
        return response()->json($escuela->load([
            'localidad.departamento.provincia', 
            'ambito', 
            'dependencia', 
            'sector'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EscuelaRequest $request, Escuela $escuela): JsonResponse
    {
        $this->authorize('update', $escuela);
        $dto = \App\DTOs\Escuela\UpdateEscuelaDTO::fromRequest($request);
        $escuela = $this->escuelaService->update($escuela, $dto);
        return response()->json($escuela);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Escuela $escuela): JsonResponse
    {
        $this->authorize('delete', $escuela);
        $this->escuelaService->delete($escuela);
        return response()->json(null, 204);
    }
}
