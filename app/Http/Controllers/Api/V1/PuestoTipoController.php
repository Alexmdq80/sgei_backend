<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PuestoTipo;
use App\Services\PuestoTipoService;
use App\Http\Requests\Api\V1\PuestoTipoRequest;
use Illuminate\Http\JsonResponse;

class PuestoTipoController extends Controller
{
    public function __construct(
        protected PuestoTipoService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(PuestoTipoRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function update(PuestoTipoRequest $request, PuestoTipo $puestoTipo): JsonResponse
    {
        $item = $this->service->update($puestoTipo, $request->validated());
        return response()->json($item);
    }

    public function destroy(PuestoTipo $puestoTipo): JsonResponse
    {
        $this->service->delete($puestoTipo);
        return response()->json(null, 204);
    }
}
