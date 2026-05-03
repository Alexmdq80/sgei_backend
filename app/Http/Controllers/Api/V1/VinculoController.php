<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vinculo;
use App\Services\VinculoService;
use App\Http\Requests\Api\V1\VinculoRequest;
use Illuminate\Http\JsonResponse;

class VinculoController extends Controller
{
    public function __construct(
        protected VinculoService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(VinculoRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function update(VinculoRequest $request, Vinculo $vinculo): JsonResponse
    {
        $item = $this->service->update($vinculo, $request->validated());
        return response()->json($item);
    }

    public function destroy(Vinculo $vinculo): JsonResponse
    {
        $this->service->delete($vinculo);
        return response()->json(null, 204);
    }
}
