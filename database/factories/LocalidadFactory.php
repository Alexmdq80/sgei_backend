<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Localidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Localidad>
 */
class LocalidadFactory extends Factory
{
    protected $model = Localidad::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->city(),
            'departamento_id' => Departamento::factory(),
        ];
    }
}
