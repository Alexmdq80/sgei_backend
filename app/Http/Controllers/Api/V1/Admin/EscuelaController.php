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
        return response()->json($this->escuelaService->getAllAdmin($search));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EscuelaRequest $request): JsonResponse
    {
        $this->authorize('create', Escuela::class);
        $escuela = $this->escuelaService->create($request->validated());
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
        $escuela = $this->escuelaService->update($escuela, $request->validated());
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
