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
            'nombre' => $this->faker->word() . ' Año',
            'vigente' => true,
        ];
    }
}
