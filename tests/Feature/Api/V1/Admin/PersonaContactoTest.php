<?php

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

// --- Autenticación / autorización ---

test('no autenticado no puede leer contacto', function () {
    $persona = Persona::factory()->create();
    $this->getJson("/api/v1/admin/personas/{$persona->id}/contacto")->assertStatus(401);
});

test('no autenticado no puede actualizar contacto', function () {
    $persona = Persona::factory()->create();
    $this->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [])->assertStatus(401);
});

test('usuario sin permisos no puede leer ni actualizar contacto', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->usuarioSinPermisos, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/contacto")->assertStatus(403);
    $this->actingAs($this->usuarioSinPermisos, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [])->assertStatus(403);
});

// --- Lectura ---

test('admin puede leer contacto vacío (aún no tiene)', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/contacto")->assertOk();
});

// --- Escritura ---

test('admin crea contacto', function () {
    $persona = Persona::factory()->create();

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [
            'telefono' => '44668899',
            'email' => 'persona@test.com',
        ])->assertOk();

    $this->assertDatabaseHas('contactos', [
        'persona_id' => $persona->id,
        'telefono' => '44668899',
        'email' => 'persona@test.com',
    ]);
});

test('admin actualiza contacto sin pisar valores previos (filtra null)', function () {
    $persona = Persona::factory()->create();
    $persona->contacto()->create(['telefono' => '123', 'email' => 'a@b.com']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [
            'telefono' => '777',
        ])->assertOk();

    // El email previo NO debe pisarse
    $this->assertDatabaseHas('contactos', [
        'persona_id' => $persona->id,
        'telefono' => '777',
        'email' => 'a@b.com',
    ]);
});

// --- Validaciones (Reglas del PersonaContactoRequest) ---

test('valida email con formato inválido', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [
            'email' => 'no-es-email',
        ])->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('valida telefono que contiene letras', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [
            'telefono' => 'abc',
        ])->assertStatus(422)
        ->assertJsonValidationErrors('telefono');
});

test('valida unicidad de email en otra persona', function () {
    $persona = Persona::factory()->create();
    $otra = Persona::factory()->create();
    $otra->contacto()->create(['email' => 'ocupado@test.com']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/contacto", [
            'email' => 'ocupado@test.com',
        ])->assertStatus(422)
        ->assertJsonValidationErrors('email');
});
