<?php

namespace Database\Factories;

use App\Models\Escuela;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Escuela>
 */
class EscuelaFactory extends Factory
{
    protected $model = Escuela::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Escuela ' . $this->faker->unique()->company(),
            'numero' => $this->faker->numerify('####'),
            'cue_anexo' => $this->faker->numerify('#########'),
            'sector_id' => Sector::factory(),
            'modalidad_comun' => true,
            'domicilio' => $this->faker->address(),
            'telefono' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
        ];
    }
}
