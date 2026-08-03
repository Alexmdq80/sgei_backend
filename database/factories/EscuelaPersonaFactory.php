<?php

namespace Database\Factories;

use App\Models\Escuela;
use App\Models\Persona;
use App\Models\EscuelaPersona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EscuelaPersona>
 */
class EscuelaPersonaFactory extends Factory
{
    protected $model = EscuelaPersona::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'escuela_id' => Escuela::factory(),
            'persona_id' => Persona::factory(),
            'role_id' => \Spatie\Permission\Models\Role::where('name', 'profesor')->first()?->id ?? 1,
            'verified_at' => null, // Pendiente por defecto
        ];
    }

    /**
     * State for a verified request.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => now(),
        ]);
    }
}
