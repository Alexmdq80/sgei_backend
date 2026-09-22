<?php

namespace Database\Factories;

use App\Models\DocumentoTipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentoTipo>
 */
class DocumentoTipoFactory extends Factory
{
    protected $model = DocumentoTipo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement(['DNI', 'Pasaporte', 'CUIL', 'CUIT', 'LE', 'LC']),
            'vigente' => true,
        ];
    }
}
