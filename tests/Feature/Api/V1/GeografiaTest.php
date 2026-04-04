<?php

use App\Models\Departamento;
use App\Models\Localidad;
use App\Models\Provincia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list provinces', function () {
    Provincia::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/provincias');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('can list departments by province', function () {
    $provincia = Provincia::factory()->create();
    Departamento::factory()->count(2)->create(['provincia_id' => $provincia->id]);
    Departamento::factory()->create(); // Otro departamento de otra provincia

    $response = $this->getJson("/api/v1/departamentos?provincia_id={$provincia->id}");

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('can list localities by department', function () {
    $departamento = Departamento::factory()->create();
    Localidad::factory()->count(2)->create(['departamento_id' => $departamento->id]);
    Localidad::factory()->create(); // Otra localidad

    $response = $this->getJson("/api/v1/localidades?departamento_id={$departamento->id}");

    $response->assertStatus(200)
        ->assertJsonCount(2);
});
