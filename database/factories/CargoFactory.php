<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cargo;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cargo>
 */
class CargoFactory extends Factory
{
    protected $model = Cargo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word(),
            'requiere_cursos' => false,
            'activo' => true,
        ];
    }
}
