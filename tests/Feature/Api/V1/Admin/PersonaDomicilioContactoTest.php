<?php

use App\Models\Calle;
use App\Models\Contacto;
use App\Models\Domicilio;
use App\Models\Localidad;
use App\Models\LocalidadCensal;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->admin = Usuario::factory()->create(['es_administrador' => true]);
    $this->admin->assignRole('superuser');

    $this->usuarioSinPermisos = Usuario::factory()->create();
});

test('no autenticado no puede leer domicilio-contacto', function () {
    $persona = Persona::factory()->create();
    $this->getJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto")->assertStatus(401);
});

test('no autenticado no puede actualizar domicilio-contacto', function () {
    $persona = Persona::factory()->create();
    $this->putJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto", [])->assertStatus(401);
});

test('usuario sin permisos no puede leer ni actualizar', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->usuarioSinPermisos, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto")->assertStatus(403);
    $this->actingAs($this->usuarioSinPermisos, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto", [])->assertStatus(403);
});

test('admin puede leer domicilio y contacto vacíos', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto")
        ->assertOk()
        ->assertJsonStructure(['domicilio', 'contacto']);
});

test('admin crea domicilio y contacto', function () {
    $persona = Persona::factory()->create();
    $localidad = Localidad::factory()->create();
    $calle = Calle::create(['nombre' => 'AV. RIVADAVIA']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto", [
            'telefono' => '44668899',
            'email' => 'persona@test.com',
            'localidad_id' => $localidad->id,
            'calle_id' => $calle->id,
            'numero' => '123',
        ])->assertOk();

    $this->assertDatabaseHas('contactos', [
        'persona_id' => $persona->id,
        'telefono' => '44668899',
        'email' => 'persona@test.com',
    ]);
    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'numero' => '123',
    ]);
});

test('admin actualiza sin pisar valores previos (filtra null)', function () {
    $persona = Persona::factory()->create();
    $persona->contacto()->create(['telefono' => '123', 'email' => 'a@b.com']);
    $persona->domicilio()->create(['numero' => '99']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto", [
            'telefono' => '777', // envía solo teléfono
        ])->assertOk();

    $this->assertDatabaseHas('contactos', [
        'persona_id' => $persona->id,
        'telefono' => '777',
        'email' => 'a@b.com', // NO se pisó
    ]);
});

test('valida teléfono con letras y email inválido', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio-contacto", [
            'telefono' => 'abc',
            'email' => 'no-es-email',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['telefono', 'email']);
});

test('busca calles por q', function () {
    Calle::create(['nombre' => 'AV. RIVADAVIA']);
    Calle::create(['nombre' => 'SAN MARTÍN']);

    $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/calles?q=RIVADAVIA')
        ->assertOk();
});
