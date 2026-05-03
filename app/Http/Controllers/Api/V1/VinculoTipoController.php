<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VinculoTipo;
use App\Services\VinculoTipoService;
use App\Http\Requests\Api\V1\VinculoTipoRequest;
use Illuminate\Http\JsonResponse;

class VinculoTipoController extends Controller
{
    public function __construct(
        protected VinculoTipoService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(VinculoTipoRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function update(VinculoTipoRequest $request, VinculoTipo $vinculoTipo): JsonResponse
    {
        $item = $this->service->update($vinculoTipo, $request->validated());
        return response()->json($item);
    }

    public function destroy(VinculoTipo $vinculoTipo): JsonResponse
    {
        $this->service->delete($vinculoTipo);
        return response()->json(null, 204);
    }
}
