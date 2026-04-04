<?php

use App\Models\Escuela;
use App\Models\Nivel;
use App\Models\Sector;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Asegurar que los roles existan
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('can list schools', function () {
    Escuela::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/escuelas');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('can search schools by name', function () {
    Escuela::factory()->create(['nombre' => 'Escuela Normal']);
    Escuela::factory()->create(['nombre' => 'Colegio Nacional']);

    $response = $this->getJson('/api/v1/escuelas?search=Normal');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['nombre' => 'Escuela Normal']);
});

test('can get niveles', function () {
    Nivel::factory()->create(['nombre' => 'Primario', 'vigente' => true]);
    Nivel::factory()->create(['nombre' => 'Secundario', 'vigente' => true]);
    Nivel::factory()->create(['nombre' => 'Obsoleto', 'vigente' => false]);

    $response = $this->getJson('/api/v1/niveles');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonFragment(['nombre' => 'Primario'])
        ->assertJsonMissing(['nombre' => 'Obsoleto']);
});

test('can get sectores', function () {
    Sector::factory()->create(['nombre' => 'Estatal', 'vigente' => true]);
    Sector::factory()->create(['nombre' => 'Privado', 'vigente' => true]);

    $response = $this->getJson('/api/v1/sectores');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonFragment(['nombre' => 'Estatal']);
});

test('authenticated user can request to join a school', function () {
    $user = Usuario::factory()->create();
    $escuela = Escuela::factory()->create();
    $rol = Role::where('name', 'profesor')->first();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/auth/escuelas/join', [
            'escuela_id' => $escuela->id,
            'role_id' => $rol->id
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['message' => 'Solicitud enviada con éxito. Espere la aprobación del administrador.']);

    $this->assertDatabaseHas('escuela_usuario', [
        'usuario_id' => $user->id,
        'escuela_id' => $escuela->id,
        'role_id' => $rol->id,
        'verified_at' => null
    ]);
});

test('authenticated user can cancel join request', function () {
    $user = Usuario::factory()->create();
    $escuela = Escuela::factory()->create();
    $rol = Role::where('name', 'profesor')->first();

    // Crear la solicitud previa usando el modelo EscuelaUsuario
    \App\Models\EscuelaUsuario::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'usuario_id' => $user->id,
        'escuela_id' => $escuela->id,
        'role_id' => $rol->id,
        'verified_at' => null
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/auth/escuelas/cancel-join', [
            'escuela_id' => $escuela->id
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['message' => 'Solicitud cancelada.']);

    $this->assertSoftDeleted('escuela_usuario', [
        'usuario_id' => $user->id,
        'escuela_id' => $escuela->id
    ]);
});
