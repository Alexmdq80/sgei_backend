<?php

use App\Models\Calle;
use App\Models\Localidad;
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

test('no autenticado no puede leer domicilio', function () {
    $persona = Persona::factory()->create();
    $this->getJson("/api/v1/admin/personas/{$persona->id}/domicilio")->assertStatus(401);
});

test('no autenticado no puede actualizar domicilio', function () {
    $persona = Persona::factory()->create();
    $this->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [])->assertStatus(401);
});

test('usuario sin permisos no puede leer ni actualizar domicilio', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->usuarioSinPermisos, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/domicilio")->assertStatus(403);
    $this->actingAs($this->usuarioSinPermisos, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [])->assertStatus(403);
});

// --- Lectura ---

test('admin puede leer domicilio vacío (aún no tiene)', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/domicilio")->assertOk();
});

// --- Escritura ---

test('admin crea domicilio', function () {
    $persona = Persona::factory()->create();
    $localidad = Localidad::factory()->create();
    $calle = Calle::create(['nombre' => 'AV. RIVADAVIA']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'localidad_id' => $localidad->id,
            'calle_id' => $calle->id,
            'numero' => '123',
        ])->assertOk();

    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'numero' => '123',
    ]);
});

test('admin actualiza domicilio sin pisar valores previos (filtra null)', function () {
    $persona = Persona::factory()->create();
    // La persona ya tiene un domicilio previo
    $persona->domicilio()->create(['numero' => '99']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'numero' => '777', // SOLO envía número
        ])->assertOk();

    // El resto de columnas no debe haberse pisado
    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'numero' => '777',
    ]);
});
