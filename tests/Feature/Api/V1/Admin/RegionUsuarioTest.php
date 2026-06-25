<?php

use App\Models\Usuario;
use App\Models\Region;
use App\Models\RegionUsuario;
use App\Models\Provincia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seeders necesarios
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);

    // Setup Provincias
    $this->provinciaA = Provincia::factory()->create(['nombre' => 'Provincia A']);
    $this->provinciaB = Provincia::factory()->create(['nombre' => 'Provincia B']);

    // Setup Regiones
    $this->regionA = Region::create(['provincia_id' => $this->provinciaA->id, 'numero' => 1, 'vigente' => true]);
    $this->regionB = Region::create(['provincia_id' => $this->provinciaB->id, 'numero' => 2, 'vigente' => true]);

    // 1. Setup Superuser
    $this->superuser = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->superuser->assignRole('superuser');

    // 2. Setup Jefe Provincial for Provincia A
    $this->jefeProvincialA = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->jefeProvincialA->assignRole('jefe_provincial');
    \App\Models\ProvinciaUsuario::create([
        'usuario_id' => $this->jefeProvincialA->id,
        'provincia_id' => $this->provinciaA->id
    ]);

    // 3. Setup Jefe Provincial for Provincia B
    $this->jefeProvincialB = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->jefeProvincialB->assignRole('jefe_provincial');
    \App\Models\ProvinciaUsuario::create([
        'usuario_id' => $this->jefeProvincialB->id,
        'provincia_id' => $this->provinciaB->id
    ]);

    // 4. Setup Target Users
    $this->userRegional1 = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->userRegional2 = Usuario::factory()->create(['email_verified_at' => now()]);
});

test('superuser puede listar todos los jefes regionales', function () {
    RegionUsuario::create(['usuario_id' => $this->userRegional1->id, 'region_id' => $this->regionA->id]);
    RegionUsuario::create(['usuario_id' => $this->userRegional2->id, 'region_id' => $this->regionB->id]);

    $response = $this->actingAs($this->superuser, 'sanctum')
        ->getJson('/api/v1/admin/regiones-usuarios');

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('jefe provincial solo puede listar jefes regionales de su provincia', function () {
    RegionUsuario::create(['usuario_id' => $this->userRegional1->id, 'region_id' => $this->regionA->id]);
    RegionUsuario::create(['usuario_id' => $this->userRegional2->id, 'region_id' => $this->regionB->id]);

    // Jefe Provincial A (Provincia A) should only see regionA
    $response = $this->actingAs($this->jefeProvincialA, 'sanctum')
        ->getJson('/api/v1/admin/regiones-usuarios');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['region_id' => $this->regionA->id])
        ->assertJsonMissing(['region_id' => $this->regionB->id]);
});

test('jefe provincial puede asignar jefe regional en su provincia', function () {
    $response = $this->actingAs($this->jefeProvincialA, 'sanctum')
        ->postJson('/api/v1/admin/regiones-usuarios', [
            'usuario_id' => $this->userRegional1->id,
            'region_id' => $this->regionA->id
        ]);

    $response->assertStatus(201);
    expect($this->userRegional1->fresh()->hasRole('jefe_regional'))->toBeTrue();
});

test('jefe provincial no puede asignar jefe regional en otra provincia', function () {
    $response = $this->actingAs($this->jefeProvincialA, 'sanctum')
        ->postJson('/api/v1/admin/regiones-usuarios', [
            'usuario_id' => $this->userRegional1->id,
            'region_id' => $this->regionB->id
        ]);

    $response->assertStatus(403);
});

test('jefe provincial puede remover jefe regional en su provincia', function () {
    $assoc = RegionUsuario::create(['usuario_id' => $this->userRegional1->id, 'region_id' => $this->regionA->id]);
    $this->userRegional1->assignRole('jefe_regional');

    $response = $this->actingAs($this->jefeProvincialA, 'sanctum')
        ->deleteJson("/api/v1/admin/regiones-usuarios/{$assoc->id}");

    $response->assertStatus(200);
    expect($this->userRegional1->fresh()->hasRole('jefe_regional'))->toBeFalse();
});

test('jefe provincial no puede remover jefe regional de otra provincia', function () {
    $assoc = RegionUsuario::create(['usuario_id' => $this->userRegional1->id, 'region_id' => $this->regionB->id]);

    $response = $this->actingAs($this->jefeProvincialA, 'sanctum')
        ->deleteJson("/api/v1/admin/regiones-usuarios/{$assoc->id}");

    $response->assertStatus(403);
});
