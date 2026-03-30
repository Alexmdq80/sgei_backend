<?php

namespace Tests\Feature\Api\V1;

use App\Models\Departamento;
use App\Models\Localidad;
use App\Models\Provincia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeografiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_provinces(): void
    {
        Provincia::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/provincias');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_list_departments_by_province(): void
    {
        $provincia = Provincia::factory()->create();
        Departamento::factory()->count(2)->create(['provincia_id' => $provincia->id]);
        Departamento::factory()->create(); // Otro departamento de otra provincia

        $response = $this->getJson("/api/v1/departamentos?provincia_id={$provincia->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_can_list_localities_by_department(): void
    {
        $departamento = Departamento::factory()->create();
        Localidad::factory()->count(2)->create(['departamento_id' => $departamento->id]);
        Localidad::factory()->create(); // Otra localidad

        $response = $this->getJson("/api/v1/localidades?departamento_id={$departamento->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }
}
