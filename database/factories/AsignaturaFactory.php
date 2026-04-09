<?php

namespace Database\Factories;

use App\Models\Asignatura;
use App\Models\AnioPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asignatura>
 */
class AsignaturaFactory extends Factory
{
    protected $model = Asignatura::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(3, true),
            'nombre_completo' => $this->faker->sentence(5),
            'anio_plan_id' => AnioPlan::factory(),
            'horas_semanales' => $this->faker->numberBetween(2, 8),
            'codigo' => strtoupper($this->faker->bothify('ASIG-###')),
            'orden' => $this->faker->numberBetween(1, 20),
        ];
    }
}
