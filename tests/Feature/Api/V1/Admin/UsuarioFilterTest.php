<?php

use App\Models\Usuario;
use App\Models\Escuela;
use App\Models\EscuelaPersona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\Provincia;
use App\Models\Departamento;
use App\Models\Localidad;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seeders necesarios
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);

    // 1. Setup Admin
    $this->admin = Usuario::factory()->create([
        'es_administrador' => true,
        'email_verified_at' => now(),
    ]);
    $this->admin->assignRole('superuser');
    $this->admin->givePermissionTo('sistema.usuarios');

    // 2. Setup Schools
    $this->escuelaA = Escuela::factory()->create(['nombre' => 'Escuela A', 'cue_anexo' => '111111100']);
    $this->escuelaB = Escuela::factory()->create(['nombre' => 'Escuela B', 'cue_anexo' => '222222200']);

    // 3. Setup Roles
    $rol = \Spatie\Permission\Models\Role::where('name', 'director')->first();

    // 4. Setup Users with different vinculations
    
    // User 1: Vinculated to Escuela A (Verified)
    $user1 = Usuario::factory()->create(['nombre' => 'User One']);
    $persona1 = \App\Models\Persona::factory()->create(['usuario_id' => $user1->id]);
    \App\Models\EscuelaPersona::create([
        'id' => Str::uuid(),
        'persona_id' => $persona1->id,
        'escuela_id' => $this->escuelaA->id,
        'role_id' => $rol->id,
        'verified_at' => now()
    ]);

    // User 2: Vinculated to Escuela A (Pending)
    $user2 = Usuario::factory()->create(['nombre' => 'User Two']);
    $persona2 = \App\Models\Persona::factory()->create(['usuario_id' => $user2->id]);
    \App\Models\EscuelaPersona::create([
        'id' => Str::uuid(),
        'persona_id' => $persona2->id,
        'escuela_id' => $this->escuelaA->id,
        'role_id' => $rol->id,
        'verified_at' => null
    ]);

    // User 3: Vinculated to Escuela B (Verified)
    $user3 = Usuario::factory()->create(['nombre' => 'User Three']);
    $persona3 = \App\Models\Persona::factory()->create(['usuario_id' => $user3->id]);
    \App\Models\EscuelaPersona::create([
        'id' => Str::uuid(),
        'persona_id' => $persona3->id,
        'escuela_id' => $this->escuelaB->id,
        'role_id' => $rol->id,
        'verified_at' => now()
    ]);

    // User 4: No vinculations
    Usuario::factory()->create(['nombre' => 'User Four']);

    // Provincia A con su departamento y localidad
    $this->provinciaA = Provincia::factory()->create(['nombre' => 'Provincia A']);
    $this->departamentoA = Departamento::factory()->create([
        'nombre' => 'Depto A',
        'provincia_id' => $this->provinciaA->id
    ]);
    $this->localidadA = Localidad::factory()->create([
        'nombre' => 'Loc A',
        'departamento_id' => $this->departamentoA->id
    ]);

    // Provincia B con su departamento y localidad
    $this->provinciaB = Provincia::factory()->create(['nombre' => 'Provincia B']);
    $this->departamentoB = Departamento::factory()->create([
        'nombre' => 'Depto B',
        'provincia_id' => $this->provinciaB->id
    ]);
    $this->localidadB = Localidad::factory()->create([
        'nombre' => 'Loc B',
        'departamento_id' => $this->departamentoB->id
    ]);

    // Escuela C en Provincia A
    $this->escuelaC = Escuela::factory()->create([
        'nombre' => 'Escuela C',
        'cue_anexo' => '333333300',
        'localidad_id' => $this->localidadA->id
    ]);

    // Escuela D en Provincia B
    $this->escuelaD = Escuela::factory()->create([
        'nombre' => 'Escuela D',
        'cue_anexo' => '444444400',
        'localidad_id' => $this->localidadB->id
    ]);

    // User 5: Vinculado a Escuela C (Provincia A)
    $user5 = Usuario::factory()->create(['nombre' => 'User Five']);
    $persona5 = \App\Models\Persona::factory()->create(['usuario_id' => $user5->id]);
    \App\Models\EscuelaPersona::create([
        'id' => Str::uuid(),
        'persona_id' => $persona5->id,
        'escuela_id' => $this->escuelaC->id,
        'role_id' => $rol->id,
        'verified_at' => now()
    ]);

    // User 6: Vinculado a Escuela D (Provincia B)
    $user6 = Usuario::factory()->create(['nombre' => 'User Six']);
    $persona6 = \App\Models\Persona::factory()->create(['usuario_id' => $user6->id]);
    \App\Models\EscuelaPersona::create([
        'id' => Str::uuid(),
        'persona_id' => $persona6->id,
        'escuela_id' => $this->escuelaD->id,
        'role_id' => $rol->id,
        'verified_at' => now()
    ]);
});

test('el administrador puede filtrar usuarios por escuela específica', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?escuela_id={$this->escuelaA->id}");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data') // User One and User Two
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonFragment(['nombre' => 'User Two'])
        ->assertJsonMissing(['nombre' => 'User Three'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios con vinculaciones ya aprobadas', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?vinculation=vinculated");

    $response->assertStatus(200)
        ->assertJsonCount(4, 'data') // User One and User Three
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonFragment(['nombre' => 'User Three'])
        ->assertJsonMissing(['nombre' => 'User Two'])
        ->assertJsonMissing(['nombre' => 'User Four'])
        ->assertJsonFragment(['nombre' => 'User Five'])
        ->assertJsonFragment(['nombre' => 'User Six']);
});

test('el administrador puede filtrar usuarios con vinculaciones pendientes', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?vinculation=pending");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data') // Only User Two
        ->assertJsonFragment(['nombre' => 'User Two'])
        ->assertJsonMissing(['nombre' => 'User One'])
        ->assertJsonMissing(['nombre' => 'User Three'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('la respuesta de usuarios incluye el CUE y el nombre del rol escolar', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?search=User One");

    $response->assertStatus(200)
        ->assertJsonPath('data.0.escuelas_personas.0.escuela.cue_anexo', '111111100')
        ->assertJsonPath('data.0.escuelas_personas.0.role.name', 'director');
});


test('el administrador puede filtrar usuarios por rol superuser', function () {
    // Crear un superusuario adicional
    $superUser = Usuario::factory()->create([
        'nombre' => 'Super User Extra',
        'es_administrador' => true,
        'email_verified_at' => now(),
    ]);
    $superUser->assignRole('superuser');

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?role=superuser');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'Super User Extra'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});



test('el administrador puede filtrar usuarios por rol equipo_directivo', function () {
    // User One, Two, Three, Five y Six tienen rol 'director' en escuela_persona
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?role=equipo_directivo');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonFragment(['nombre' => 'User Two'])
        ->assertJsonFragment(['nombre' => 'User Three'])
        ->assertJsonFragment(['nombre' => 'User Five'])
        ->assertJsonFragment(['nombre' => 'User Six'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios por rol profesor', function () {
    // Obtener el rol profesor del seeder
    $rolProfesor = \Spatie\Permission\Models\Role::where('name', 'profesor')->first();

    // Crear un usuario vinculado a una escuela con rol profesor
    $profesor = Usuario::factory()->create(['nombre' => 'Profesor Test']);
    $personaProfesor = \App\Models\Persona::factory()->create(['usuario_id' => $profesor->id]);
    \App\Models\EscuelaPersona::create([
        'id' => Str::uuid(),
        'persona_id' => $personaProfesor->id,
        'escuela_id' => $this->escuelaA->id,
        'role_id' => $rolProfesor->id,
        'verified_at' => now()
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?role=profesor');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'Profesor Test'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios con contraseña definida', function () {
    // Usuario con contraseña definida (password_set = true)
    $usuarioConClave = Usuario::factory()->create([
        'nombre' => 'Usuario Con Clave',
        'password_set' => true,
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?password_set=true');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'Usuario Con Clave'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios con invitación pendiente (sin contraseña)', function () {
    // Usuario con contraseña definida que NO debe aparecer
    Usuario::factory()->create([
        'nombre' => 'Usuario Con Clave',
        'password_set' => true,
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?password_set=false');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'User Four'])
        ->assertJsonMissing(['nombre' => 'Usuario Con Clave']);
});

test('el administrador puede filtrar usuarios con email verificado', function () {
    // Usuario sin email verificado que NO debe aparecer
    Usuario::factory()->unverified()->create([
        'nombre' => 'Email Sin Verificar',
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?email_verified=verified');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonMissing(['nombre' => 'Email Sin Verificar']);
});

test('el administrador puede filtrar usuarios con email sin verificar', function () {
    $usuarioSinVerificar = Usuario::factory()->unverified()->create([
        'nombre' => 'Email Sin Verificar',
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?email_verified=unverified');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'Email Sin Verificar'])
        ->assertJsonMissing(['nombre' => 'User One']);
});

test('el administrador puede filtrar usuarios vinculados al padrón', function () {
    // User One a Six tienen persona vinculada; User Four no tiene
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?persona_linked=linked');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios sin vincular al padrón', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/usuarios?persona_linked=unlinked');

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'User Four'])
        ->assertJsonMissing(['nombre' => 'User One']);
});
