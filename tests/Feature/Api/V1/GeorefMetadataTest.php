<?php

use App\Models\GeorefFuente;
use App\Models\GeorefCategoria;
use App\Models\GeorefFuncion;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    $this->admin = Usuario::factory()->create();
    $this->admin->assignRole('superuser');
    $this->admin->givePermissionTo('sistema.usuarios');
});

test('can list georef sources', function () {
    GeorefFuente::factory()->count(3)->create();

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/georef-fuentes');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('can create georef source in uppercase', function () {
    $data = [
        'nombre' => 'fuente de prueba',
        'orden' => 10,
        'vigente' => true
    ];

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/georef-fuentes', $data);

    $response->assertStatus(201)
        ->assertJsonPath('nombre', 'FUENTE DE PRUEBA');

    $this->assertDatabaseHas('georef_fuentes', [
        'nombre' => 'FUENTE DE PRUEBA'
    ]);
});

test('can search georef source by name', function () {
    GeorefFuente::factory()->create(['nombre' => 'ALFA']);
    GeorefFuente::factory()->create(['nombre' => 'BETA']);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/georef-fuentes?search=alfa');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.nombre', 'ALFA');
});

test('validation error returns standard format', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/georef-fuentes', ['nombre' => '']);

    $response->assertStatus(400)
        ->assertJsonStructure(['error', 'code'])
        ->assertJsonPath('code', 400);
});

test('can update georef category', function () {
    $categoria = GeorefCategoria::factory()->create(['nombre' => 'CAT OLD']);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/georef-categorias/{$categoria->id}", [
            'nombre' => 'cat new'
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('nombre', 'CAT NEW');

    $this->assertDatabaseHas('georef_categorias', [
        'id' => $categoria->id,
        'nombre' => 'CAT NEW'
    ]);
});

test('can delete georef funcion', function () {
    $funcion = GeorefFuncion::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/v1/admin/georef-funcions/{$funcion->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('georef_funcions', ['id' => $funcion->id]);
});
