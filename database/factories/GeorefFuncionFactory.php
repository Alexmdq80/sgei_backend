<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GeorefFuncion>
 */
class GeorefFuncionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => mb_strtoupper($this->faker->unique()->word()),
            'orden' => $this->faker->numberBetween(1, 100),
            'vigente' => true,
        ];
    }
}
