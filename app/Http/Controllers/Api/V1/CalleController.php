<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CalleRequest;
use App\Models\Calle;
use App\Services\CalleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalleController extends Controller
{
    public function __construct(
        protected CalleService $calleService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $localidadId = $request->filled('localidad_id') ? (int) $request->localidad_id : null;

        // Si se pide el listado compacto por localidad para IndexedDB
        if ($request->boolean('compact') && $localidadId) {
            return response()->json(
                $this->calleService->getCompactByLocalidad($localidadId)
            );
        }

        $search = $request->query('q', $request->query('search'));
        $perPage = $request->query('per_page', 20);

        return response()->json(
            $this->calleService->getAll(
                is_string($search) ? $search : null,
                (int) $perPage,
                $localidadId
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CalleRequest $request): JsonResponse
    {
        $item = $this->calleService->create($request->validated());

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->calleService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CalleRequest $request, Calle $calle): JsonResponse
    {
        $updated = $this->calleService->update($calle, $request->validated());

        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calle $calle): JsonResponse
    {
        $this->calleService->delete($calle);

        return response()->json(null, 204);
    }
}
