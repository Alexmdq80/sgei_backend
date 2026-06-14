<?php

namespace Database\Factories;

use App\Models\Cupof;
use App\Models\Escuela;
use App\Models\Escalafon;
use App\Models\PuestoTipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cupof>
 */
class CupofFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo_cupof' => $this->faker->unique()->bothify('CUPOF-####-????'),
            'escuela_id' => Escuela::factory(),
            'escalafon_id' => Escalafon::first() ? Escalafon::first()->id : 1,
            'puesto_tipo_id' => PuestoTipo::first() ? PuestoTipo::first()->id : 1,
            'nombre_cargo' => $this->faker->jobTitle(),
            'cantidad' => 1,
            'estado_cupof' => 'VACANTE',
        ];
    }
}
