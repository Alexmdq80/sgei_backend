<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'documento_tipo_id' => 1,
            'documento_numero' => fake()->unique()->numerify('########'),
            'apellido' => fake()->lastName(),
            'nombre' => fake()->firstName(),
            'vive_si' => true,
            'nacimiento_fecha' => fake()->date(),
        ];
    }
}
