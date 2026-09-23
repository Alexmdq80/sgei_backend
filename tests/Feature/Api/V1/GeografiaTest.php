<?php

use App\Models\Departamento;
use App\Models\Localidad;
use App\Models\Nacion;
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
test('can search localities with full hierarchy (omnibox)', function () {
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $provincia = Provincia::factory()->create(['nacion_id' => $nacion->id]);
    $departamento = Departamento::factory()->create(['provincia_id' => $provincia->id]);
    $localidad = Localidad::factory()->create([
        'nombre' => 'TANDIL',
        'departamento_id' => $departamento->id,
    ]);
    // Localidad ajena al término: debe quedar fuera del resultado
    Localidad::factory()->create(['nombre' => 'QUILMES']);

    $response = $this->getJson('/api/v1/localidades?search=TANDIL&per_page=15');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonPath('0.nombre', 'TANDIL')
        ->assertJsonPath('0.departamento.provincia.nacion.nombre', 'ARGENTINA');
});

test('keeps legacy departamento_id filter when no search is sent', function () {
    $departamento = Departamento::factory()->create();
    Localidad::factory()->count(2)->create(['departamento_id' => $departamento->id]);
    Localidad::factory()->create(); // otra localidad

    $response = $this->getJson("/api/v1/localidades?departamento_id={$departamento->id}");

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('can get full localities catalog with hierarchy', function () {
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $provincia = Provincia::factory()->create(['nacion_id' => $nacion->id]);
    $departamento = Departamento::factory()->create(['provincia_id' => $provincia->id]);
    Localidad::factory()->count(2)->create([
        'nombre' => 'LOCALIDAD DE PRUEBA',
        'departamento_id' => $departamento->id,
    ]);

    $response = $this->getJson('/api/v1/localidades/catalogo-completo');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonPath('0.departamento.id', $departamento->id)
        ->assertJsonPath('0.departamento.provincia.id', $provincia->id)
        ->assertJsonPath('0.departamento.provincia.nombre', $provincia->nombre);
});
