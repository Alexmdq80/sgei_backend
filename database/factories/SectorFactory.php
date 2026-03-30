<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sector>
 */
class SectorFactory extends Factory
{
    protected $model = Sector::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Estatal', 'Privado', 'Social']),
            'orden' => $this->faker->numberBetween(1, 10),
            'vigente' => true,
        ];
    }
}
