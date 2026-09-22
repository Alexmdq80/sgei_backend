<?php

namespace Database\Factories;

use App\Models\Nivel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nivel>
 */
class NivelFactory extends Factory
{
    protected $model = Nivel::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Primario', 'Secundario', 'Inicial', 'Superior']),
            'vigente' => true,
        ];
    }
}
