<?php

namespace Database\Factories;

use App\Models\PlanCiclo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanCiclo>
 */
class PlanCicloFactory extends Factory
{
    protected $model = PlanCiclo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(3, true),
            'orden' => $this->faker->numberBetween(1, 100),
            'vigente' => true,
        ];
    }
}
