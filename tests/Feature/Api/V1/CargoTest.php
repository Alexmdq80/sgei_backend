<?php

namespace Tests\Feature\Api\V1;

use App\Models\Cargo;
use App\Models\Usuario;
use Tests\ProvidesRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, ProvidesRoles::class);

beforeEach(function () {
    $this->seedRoles();
    
    $this->user = Usuario::factory()->create([
        'estado' => 'activo',
        'email_verified_at' => now(),
    ]);
});

test('un usuario autenticado puede listar cargos activos', function () {
    Cargo::factory()->count(3)->create(['activo' => true]);
    Cargo::factory()->create(['activo' => false]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v1/cargos');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('un usuario no autenticado no puede listar cargos', function () {
    $response = $this->getJson('/api/v1/cargos');
    $response->assertStatus(401);
});

test('un administrador puede crear un cargo', function () {
    $this->user->assignRole('superuser');

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/admin/cargos', [
            'nombre' => 'Cargo Nuevo',
            'tipo' => 'cargo',
            'requiere_cursos' => true
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('nombre', 'CARGO NUEVO');

    $this->assertDatabaseHas('cargos', ['nombre' => 'CARGO NUEVO']);
});

test('no se puede crear un cargo con un nombre que ya existe', function () {
    $this->user->assignRole('superuser');
    Cargo::factory()->create(['nombre' => 'CARGO EXISTENTE']);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/admin/cargos', [
            'nombre' => 'Cargo Existente',
            'requiere_cursos' => false
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['nombre']);
});

test('un usuario sin permisos no puede crear un cargo', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/admin/cargos', [
            'nombre' => 'Cargo Nuevo',
            'tipo' => 'cargo',
            'requiere_cursos' => true
        ]);

    $response->assertStatus(403);
});

test('un administrador puede actualizar un cargo', function () {
    $this->user->assignRole('superuser');
    $cargo = Cargo::factory()->create(['nombre' => 'CARGO VIEJO']);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/v1/admin/cargos/{$cargo->id}", [
            'nombre' => 'Cargo Actualizado',
            'tipo' => 'horas',
            'activo' => false
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('nombre', 'CARGO ACTUALIZADO')
        ->assertJsonPath('activo', false);

    $this->assertDatabaseHas('cargos', [
        'id' => $cargo->id,
        'nombre' => 'CARGO ACTUALIZADO',
        'activo' => false
    ]);
});

test('un administrador puede eliminar un cargo', function () {
    $this->user->assignRole('superuser');
    $cargo = Cargo::factory()->create();

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/v1/admin/cargos/{$cargo->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('cargos', ['id' => $cargo->id]);
});

test('el nombre del cargo se normaliza a mayúsculas', function () {
    $cargo = Cargo::factory()->create(['nombre' => 'preceptor']);
    $this->assertEquals('PRECEPTOR', $cargo->nombre);
    $this->assertDatabaseHas('cargos', ['nombre' => 'PRECEPTOR']);
});

test('el seeder de cargos carga los datos iniciales correctamente', function () {
    $this->artisan('db:seed', ['--class' => 'CargoSeeder']);
    
    $this->assertDatabaseHas('cargos', [
        'nombre' => 'PRECEPTOR/A',
        'requiere_cursos' => true
    ]);
    
    $this->assertDatabaseHas('cargos', [
        'nombre' => 'DIRECTOR/A',
        'requiere_cursos' => false
    ]);
});
