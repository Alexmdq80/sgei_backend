<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Escalafon;
use App\Services\EscalafonService;
use App\Http\Requests\Api\V1\EscalafonRequest;
use Illuminate\Http\JsonResponse;

class EscalafonController extends Controller
{
    public function __construct(
        protected EscalafonService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function store(EscalafonRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->getById($id));
    }

    public function update(EscalafonRequest $request, Escalafon $escalafon): JsonResponse
    {
        $item = $this->service->update($escalafon, $request->validated());
        return response()->json($item);
    }

    public function destroy(Escalafon $escalafon): JsonResponse
    {
        $this->service->delete($escalafon);
        return response()->json(null, 204);
    }
}
