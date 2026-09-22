<?php

namespace Database\Factories;

use App\Models\GeorefCategoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeorefCategoria>
 */
class GeorefCategoriaFactory extends Factory
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
