<?php

namespace Database\Factories;

use App\Models\Nivel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nivel>
 */
class NivelFactory extends Factory
{
    protected $model = Nivel::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Primario', 'Secundario', 'Inicial', 'Superior']),
            'orden' => $this->faker->numberBetween(1, 10),
            'vigente' => true,
        ];
    }
}
