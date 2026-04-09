<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\PlanCiclo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_ciclo_id' => PlanCiclo::factory(),
            'nombre' => $this->faker->words(4, true),
            'nombre_completo' => $this->faker->sentence(10),
            'duracion_anios' => $this->faker->numberBetween(1, 6),
            'resolucion' => 'Res. ' . $this->faker->numberBetween(100, 999) . '/' . $this->faker->year(),
            'orientacion' => $this->faker->word(),
        ];
    }
}
