<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Services\TurnoService;
use App\Http\Requests\Api\V1\TurnoRequest;
use Illuminate\Http\JsonResponse;

class TurnoController extends Controller
{
    public function __construct(
        protected TurnoService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(TurnoRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function update(TurnoRequest $request, Turno $turno): JsonResponse
    {
        $item = $this->service->update($turno, $request->validated());
        return response()->json($item);
    }

    public function destroy(Turno $turno): JsonResponse
    {
        $this->service->delete($turno);
        return response()->json(null, 204);
    }
}
