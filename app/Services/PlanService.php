<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PlanService
{
    /**
     * Get all plans.
     */
    public function getAllPlans(): Collection
    {
        return Plan::with('planCiclo')->get();
    }

    /**
     * Get a single plan by ID.
     */
    public function getPlanById(int $id): Plan
    {
        return Plan::with(['planCiclo', 'anioPlanes.anio'])->findOrFail($id);
    }

    /**
     * Create a new plan.
     */
    public function createPlan(array $data): Plan
    {
        return DB::transaction(function () use ($data) {
            return Plan::create($data);
        });
    }

    /**
     * Update an existing plan.
     */
    public function updatePlan(int $id, array $data): Plan
    {
        return DB::transaction(function () use ($id, $data) {
            $plan = Plan::findOrFail($id);
            $plan->update($data);
            return $plan;
        });
    }

    /**
     * Delete a plan (soft delete).
     */
    public function deletePlan(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $plan = Plan::findOrFail($id);
            return (bool) $plan->delete();
        });
    }
}
