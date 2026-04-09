<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AsignaturaRequest;
use App\Models\Asignatura;
use App\Services\AsignaturaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AsignaturaController extends Controller
{
    public function __construct(
        protected AsignaturaService $asignaturaService
    ) {}

    /**
     * Display a listing of asignaturas for an AnioPlan.
     */
    public function indexByAnioPlan(int $anioPlanId): JsonResponse
    {
        Gate::authorize('viewAny', Asignatura::class);
        $asignaturas = $this->asignaturaService->getByAnioPlan($anioPlanId);
        return response()->json($asignaturas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AsignaturaRequest $request): JsonResponse
    {
        Gate::authorize('create', Asignatura::class);
        $asignatura = $this->asignaturaService->create($request->validated());
        return response()->json($asignatura, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AsignaturaRequest $request, int $id): JsonResponse
    {
        $asignatura = Asignatura::findOrFail($id);
        Gate::authorize('update', $asignatura);
        $asignatura = $this->asignaturaService->update($id, $request->validated());
        return response()->json($asignatura);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $asignatura = Asignatura::findOrFail($id);
        Gate::authorize('delete', $asignatura);
        $this->asignaturaService->delete($id);
        return response()->json(null, 204);
    }
}
