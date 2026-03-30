<?php

namespace Database\Factories;

use App\Models\RolEscolar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RolEscolar>
 */
class RolEscolarFactory extends Factory
{
    protected $model = RolEscolar::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Administrador', 'Director', 'Secretario', 'Docente']),
            'orden' => $this->faker->numberBetween(1, 10),
            'vigente' => true,
        ];
    }
}
