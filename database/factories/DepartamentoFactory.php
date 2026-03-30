<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Provincia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departamento>
 */
class DepartamentoFactory extends Factory
{
    protected $model = Departamento::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->city(),
            'provincia_id' => Provincia::factory(),
        ];
    }
}
