<?php

use App\Models\Departamento;
use App\Models\DistritoUsuario;
use App\Models\Escuela;
use App\Models\EscuelaUsuario;
use App\Models\Localidad;
use App\Models\Provincia;
use App\Models\ProvinciaUsuario;
use App\Models\Region;
use App\Models\RegionUsuario;
use App\Models\Sector;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);

    // Geografía: Provincia A y Provincia B
    $this->provinciaA = Provincia::factory()->create(['nombre' => 'PROVINCIA A']);
    $this->provinciaB = Provincia::factory()->create(['nombre' => 'PROVINCIA B']);

    // Región dentro de Provincia A
    $this->regionA = Region::create(['provincia_id' => $this->provinciaA->id, 'numero' => 1, 'vigente' => true]);

    // Departamentos: uno en cada provincia
    $this->deptoA = Departamento::factory()->create(['provincia_id' => $this->provinciaA->id, 'region_id' => $this->regionA->id]);
    $this->deptoB = Departamento::factory()->create(['provincia_id' => $this->provinciaB->id]);

    // Localidades: una por departamento
    $this->localidadA = Localidad::factory()->create(['departamento_id' => $this->deptoA->id]);
    $this->localidadB = Localidad::factory()->create(['departamento_id' => $this->deptoB->id]);

    // Sector requerido por EscuelaFactory
    $sectorA = Sector::factory()->create();
    $sectorB = Sector::factory()->create();

    // Escuelas: una en cada provincia
    $this->escuelaA = Escuela::factory()->create(['localidad_id' => $this->localidadA->id, 'sector_id' => $sectorA->id]);
    $this->escuelaB = Escuela::factory()->create(['localidad_id' => $this->localidadB->id, 'sector_id' => $sectorB->id]);

    // Superuser
    $this->superuser = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->superuser->assignRole('superuser');

    // Jefe Provincial → Provincia A
    $this->jefeProvincial = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->jefeProvincial->assignRole('jefe_provincial');
    ProvinciaUsuario::create(['usuario_id' => $this->jefeProvincial->id, 'provincia_id' => $this->provinciaA->id]);

    // Jefe Regional → Región A (dentro de Provincia A)
    $this->jefeRegional = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->jefeRegional->assignRole('jefe_regional');
    RegionUsuario::create(['usuario_id' => $this->jefeRegional->id, 'region_id' => $this->regionA->id]);

    // Jefe Distrital → Departamento A (dentro de Provincia A)
    $this->jefeDistrital = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->jefeDistrital->assignRole('jefe_distrital');
    DistritoUsuario::create(['usuario_id' => $this->jefeDistrital->id, 'departamento_id' => $this->deptoA->id]);

    // Director de Escuela A
    $directorRole = \Spatie\Permission\Models\Role::where('name', 'director')->first();
    $this->director = Usuario::factory()->create(['email_verified_at' => now()]);
    $this->director->assignRole('director');
    EscuelaUsuario::create([
        'usuario_id'  => $this->director->id,
        'escuela_id'  => $this->escuelaA->id,
        'role_id'     => $directorRole->id,
        'verified_at' => now(),
    ]);
});

// ---------------------------------------------------------------------------
// Superuser
// ---------------------------------------------------------------------------

test('superuser puede ver comunidad de cualquier escuela', function () {
    $this->actingAs($this->superuser, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaA->id}")
        ->assertStatus(200);

    $this->actingAs($this->superuser, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaB->id}")
        ->assertStatus(200);
});

// ---------------------------------------------------------------------------
// Jefe Provincial
// ---------------------------------------------------------------------------

test('jefe provincial puede ver comunidad de escuela en su provincia', function () {
    $this->actingAs($this->jefeProvincial, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaA->id}")
        ->assertStatus(200);
});

test('jefe provincial NO puede ver comunidad de escuela fuera de su provincia', function () {
    $this->actingAs($this->jefeProvincial, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaB->id}")
        ->assertStatus(403);
});

// ---------------------------------------------------------------------------
// Jefe Regional
// ---------------------------------------------------------------------------

test('jefe regional puede ver comunidad de escuela en su region', function () {
    $this->actingAs($this->jefeRegional, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaA->id}")
        ->assertStatus(200);
});

test('jefe regional NO puede ver comunidad de escuela fuera de su region', function () {
    $this->actingAs($this->jefeRegional, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaB->id}")
        ->assertStatus(403);
});

// ---------------------------------------------------------------------------
// Jefe Distrital
// ---------------------------------------------------------------------------

test('jefe distrital puede ver comunidad de escuela en su distrito', function () {
    $this->actingAs($this->jefeDistrital, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaA->id}")
        ->assertStatus(200);
});

test('jefe distrital NO puede ver comunidad de escuela fuera de su distrito', function () {
    $this->actingAs($this->jefeDistrital, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaB->id}")
        ->assertStatus(403);
});

// ---------------------------------------------------------------------------
// Equipo de Conducción
// ---------------------------------------------------------------------------

test('director puede ver comunidad de su propia escuela', function () {
    $this->actingAs($this->director, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaA->id}")
        ->assertStatus(200);
});

test('director NO puede ver comunidad de una escuela ajena', function () {
    $this->actingAs($this->director, 'sanctum')
        ->getJson("/api/v1/admin/comunidad-educativa?escuela_id={$this->escuelaB->id}")
        ->assertStatus(403);
});

// ---------------------------------------------------------------------------
// Sin escuela_id
// ---------------------------------------------------------------------------

test('falla con 422 si no se provee escuela_id', function () {
    $this->actingAs($this->superuser, 'sanctum')
        ->getJson('/api/v1/admin/comunidad-educativa')
        ->assertStatus(422);
});
