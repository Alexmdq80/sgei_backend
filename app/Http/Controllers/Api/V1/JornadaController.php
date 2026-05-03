<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Jornada;
use App\Services\JornadaService;
use App\Http\Requests\Api\V1\JornadaRequest;
use Illuminate\Http\JsonResponse;

class JornadaController extends Controller
{
    public function __construct(
        protected JornadaService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(JornadaRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function update(JornadaRequest $request, Jornada $jornada): JsonResponse
    {
        $item = $this->service->update($jornada, $request->validated());
        return response()->json($item);
    }

    public function destroy(Jornada $jornada): JsonResponse
    {
        $this->service->delete($jornada);
        return response()->json(null, 204);
    }
}
