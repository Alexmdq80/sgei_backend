<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PlanRequest;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $planService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Plan::class);
        $plans = $this->planService->getAllPlans();
        return response()->json($plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request): JsonResponse
    {
        Gate::authorize('create', Plan::class);
        $plan = $this->planService->createPlan($request->validated());
        return response()->json($plan, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $plan = $this->planService->getPlanById($id);
        Gate::authorize('view', $plan);
        return response()->json($plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->planService->getPlanById($id);
        Gate::authorize('update', $plan);
        $plan = $this->planService->updatePlan($id, $request->validated());
        return response()->json($plan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $plan = $this->planService->getPlanById($id);
        Gate::authorize('delete', $plan);
        $this->planService->deletePlan($id);
        return response()->json(null, 204);
    }
}
