<?php

use App\Models\Calle;
use App\Models\Localidad;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Nacion;
use App\Models\Provincia;
use App\Models\Departamento;
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
    $provincia = Provincia::factory()->create();
    $departamento = Departamento::factory()->create(['provincia_id' => $provincia->id]);
    $localidad = Localidad::factory()->create(['departamento_id' => $departamento->id]);
    $calle = Calle::create(['nombre' => 'AV. RIVADAVIA']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'provincia_id' => $provincia->id,
            'departamento_id' => $departamento->id,
            'localidad_id' => $localidad->id,
            'calle_id' => $calle->id,
            'numero' => '123',
        ])->assertOk();

    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'provincia_id' => $provincia->id,
        'departamento_id' => $departamento->id,
        'localidad_id' => $localidad->id,
        'calle_id' => $calle->id,
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

test('admin crea domicilio con nacion_id y observaciones', function () {
    $persona = Persona::factory()->create();
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $provincia = Provincia::factory()->create(['nacion_id' => $nacion->id]);
    $departamento = Departamento::factory()->create(['provincia_id' => $provincia->id]);
    $localidad = Localidad::factory()->create(['departamento_id' => $departamento->id]);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'nacion_id' => $nacion->id,
            'provincia_id' => $provincia->id,
            'departamento_id' => $departamento->id,
            'localidad_id' => $localidad->id,
            'calle_id' => Calle::create(['nombre' => 'AV. RIVADAVIA'])->id,
            'numero' => '123',
            'observaciones' => 'Vive en casa propia',
        ])->assertOk();

    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'nacion_id' => $nacion->id,
        'provincia_id' => $provincia->id,
        'departamento_id' => $departamento->id,
        'localidad_id' => $localidad->id,
        'numero' => '123',
        'observaciones' => 'VIVE EN CASA PROPIA',
    ]);
});


test('admin declara domicilio desconocido y blanquea campos geográficos', function () {
    $persona = Persona::factory()->create();
    // La persona ya tenía un domicilio previo completo
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $localidad = Localidad::factory()->create();
    $persona->domicilio()->create([
        'nacion_id' => $nacion->id,
        'localidad_id' => $localidad->id,
        'numero' => '99',
    ]);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'blanquear' => true,
            'observaciones' => 'Se desconoce el domicilio actual',
        ])->assertOk();

    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'nacion_id' => null,
        'localidad_id' => null,
        'calle_id' => null,
        'numero' => null,
        'observaciones' => 'SE DESCONOCE EL DOMICILIO ACTUAL',
    ]);
});

test('GET domicilio expone la jerarquía geográfica completa', function () {
    $persona = Persona::factory()->create();
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $provincia = Provincia::factory()->create(['nacion_id' => $nacion->id]);
    $departamento = Departamento::factory()->create(['provincia_id' => $provincia->id]);
    $localidad = Localidad::factory()->create(['departamento_id' => $departamento->id]);
    $calle = Calle::create(['nombre' => 'AV. RIVADAVIA']);

    $persona->domicilio()->create([
        'nacion_id' => $nacion->id,
        'localidad_id' => $localidad->id,
        'calle_id' => $calle->id,
        'numero' => '123',
        'observaciones' => 'Casa',
    ]);

    $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/domicilio")
        ->assertOk()
        ->assertJsonPath('data.nacion_id', $nacion->id)
        ->assertJsonPath('data.nacion_nombre', 'ARGENTINA')
        ->assertJsonPath('data.provincia_id', $provincia->id)
        ->assertJsonPath('data.departamento_id', $departamento->id)
        ->assertJsonPath('data.localidad_id', $localidad->id)
        ->assertJsonPath('data.calle_id', $calle->id)
        ->assertJsonPath('data.observaciones', 'CASA');
});

test('valida nacion_id inexistente', function () {
    $persona = Persona::factory()->create();
    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'nacion_id' => 999999,
        ])->assertStatus(422)
        ->assertJsonValidationErrors('nacion_id');
});

test('admin limpia nacion_id enviándolo explícitamente en null', function () {
    $persona = Persona::factory()->create();
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $persona->domicilio()->create([
        'nacion_id' => $nacion->id,
        'numero' => '99',
    ]);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'nacion_id' => null,
        ])->assertOk();

    // Se limpia el país y NO se pisan los demás campos
    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'nacion_id' => null,
        'numero' => '99',
    ]);
});

test('admin conserva nacion_id cuando la clave se omite', function () {
    $persona = Persona::factory()->create();
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $persona->domicilio()->create([
        'nacion_id' => $nacion->id,
        'numero' => '99',
    ]);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'numero' => '777',
        ])->assertOk();

    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'nacion_id' => $nacion->id,
        'numero' => '777',
    ]);
});
test('rechaza provincia cuando nacion_id se envía explícitamente en null', function () {
    $persona = Persona::factory()->create();
    $nacion = Nacion::create(['nombre' => 'ARGENTINA']);
    $provincia = Provincia::factory()->create(['nacion_id' => $nacion->id]);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'nacion_id' => null,
            'provincia_id' => $provincia->id,
        ])->assertStatus(422)
        ->assertJsonValidationErrors('nacion_id');
});

test('persiste y expone la unidad del domicilio', function () {
    $persona = Persona::factory()->create();

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'numero' => '123',
            'unidad' => 'B',
        ])->assertOk();

    $this->assertDatabaseHas('domicilios', [
        'persona_id' => $persona->id,
        'numero' => '123',
        'unidad' => 'B',
    ]);

    $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/personas/{$persona->id}/domicilio")
        ->assertOk()
        ->assertJsonPath('data.unidad', 'B');
});

test('rechaza localidad sin departamento', function () {
    $persona = Persona::factory()->create();
    $localidad = Localidad::factory()->create();

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'localidad_id' => $localidad->id,
        ])->assertStatus(422)
        ->assertJsonValidationErrors('departamento_id');
});

test('rechaza departamento sin provincia', function () {
    $persona = Persona::factory()->create();
    $departamento = Departamento::factory()->create();

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}/domicilio", [
            'departamento_id' => $departamento->id,
        ])->assertStatus(422)
        ->assertJsonValidationErrors('provincia_id');
});
