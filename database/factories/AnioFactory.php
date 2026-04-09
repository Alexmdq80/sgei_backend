<?php

namespace Database\Factories;

use App\Models\Anio;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnioFactory extends Factory
{
    protected $model = Anio::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word() . ' Año',
            'orden' => $this->faker->numberBetween(1, 10),
            'vigente' => true,
        ];
    }
}
