<?php

namespace Database\Factories;

use App\Models\Anio;
use App\Models\AnioPlan;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnioPlanFactory extends Factory
{
    protected $model = AnioPlan::class;

    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'anio_id' => Anio::factory(),
        ];
    }
}
