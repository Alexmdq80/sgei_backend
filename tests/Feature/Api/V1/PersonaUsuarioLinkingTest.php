<?php

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Contacto;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('user status is set to vinculacion_pendiente when email is verified and match found', function () {
    // 1. Create a User
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'juan@example.com'
    ]);

    // 2. Create a Persona with matching identification and matching email in Contacto
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'juan@example.com'
    ]);

    $this->assertNull($persona->fresh()->usuario_id);

    // 3. Verify email (triggers linkToPersona)
    $user->markEmailAsVerified();

    // 4. Check if status is pending and NOT linked technically yet (waiting for confirmation)
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado);
    $this->assertNull($persona->fresh()->usuario_id, 'Technical link should NOT be automatic');
});

test('user status is set to vinculacion_pendiente when persona is created with matching email and user is verified', function () {
    // 1. Create an existing verified User
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'juan@example.com',
        'email_verified_at' => now(),
        'estado' => 'email_verificado'
    ]);

    // 2. Create a Persona with matching identification and email (triggers link via ContactoObserver)
    $response = $this->actingAs(Usuario::factory()->create(['es_administrador' => true])->assignRole('superuser'), 'sanctum')
                     ->postJson("/api/v1/admin/personas", [
                        'apellido' => 'Perez',
                        'nombre' => 'Juan',
                        'documento_tipo_id' => 1,
                        'documento_numero' => '12345678',
                        'email' => 'juan@example.com'
                     ]);

    $response->assertStatus(201);
    
    // NEW RULE: Should be pending confirmation even if verified, and NO technical link yet
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado);
    $persona = Persona::where('documento_numero', '12345678')->first();
    $this->assertNull($persona->usuario_id, 'Should NOT perform technical link automatically');
});

test('user status is set to vinculacion_pendiente when persona is created with matching email but user NOT verified', function () {
    // 1. Create an existing UNVERIFIED User
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '87654321',
        'email' => 'pedro@example.com',
        'estado' => 'email_pendiente'
    ]);

    // 2. Create a Persona with matching identification and email
    $response = $this->actingAs(Usuario::factory()->create(['es_administrador' => true])->assignRole('superuser'), 'sanctum')
                     ->postJson("/api/v1/admin/personas", [
                        'apellido' => 'Gomez',
                        'nombre' => 'Pedro',
                        'documento_tipo_id' => 1,
                        'documento_numero' => '87654321',
                        'email' => 'pedro@example.com'
                     ]);

    $response->assertStatus(201);
    
    // Check if status is pending and NOT linked technically
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado);
    $persona = Persona::where('documento_numero', '87654321')->first();
    $this->assertNull($persona->usuario_id, 'Should NOT perform technical link automatically');
});

test('admin can confirm vinculation manually', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'test@example.com'
    ]);

    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'test@example.com',
        'email_verified_at' => now(),
        'estado' => 'vinculacion_pendiente'
    ]);

    // Use PersonaController's tryLinkUser (manual action)
    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');

    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertOk();
    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
    $this->assertEquals('activo', $user->fresh()->estado);
});

test('supervisor role cannot confirm vinculation manually', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'test@example.com'
    ]);

    // Admin without appropriate role (supervisor_curricular has not enough permissions)
    $admin = Usuario::factory()->create(['es_administrador' => false]);
    $admin->assignRole('supervisor_curricular');

    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertStatus(403);
});

test('cannot confirm vinculation of unverified user manually', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'test@example.com'
    ]);

    // Unverified user
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'test@example.com',
        'estado' => 'vinculacion_pendiente'
    ]);

    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');

    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertStatus(422);
});

test('jefe regional can confirm vinculation of jefe distrital in their region', function () {
    // 1. Create a region
    $region = \App\Models\Region::create(['numero' => '99', 'vigente' => true]);
    
    // 2. Create a departamento/distrito inside that region
    $dept = \App\Models\Departamento::factory()->create([
        'region_id' => $region->id
    ]);

    // 3. Create Persona
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'distrital@example.com']);

    // 4. Create Usuario to link with role jefe_distrital
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'distrital@example.com',
        'email_verified_at' => now(),
        'estado' => 'vinculacion_pendiente'
    ]);
    $user->assignRole('jefe_distrital');
    \App\Models\DistritoUsuario::create([
        'usuario_id' => $user->id,
        'departamento_id' => $dept->id
    ]);

    // Performer is jefe regional of that region
    $performer = Usuario::factory()->create();
    $performer->assignRole('jefe_regional');
    \App\Models\RegionUsuario::create([
        'usuario_id' => $performer->id,
        'region_id' => $region->id
    ]);

    $response = $this->actingAs($performer, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertOk();
    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
});

test('jefe regional cannot confirm vinculation of jefe distrital outside their region', function () {
    $regionA = \App\Models\Region::create(['numero' => '100', 'vigente' => true]);
    $regionB = \App\Models\Region::create(['numero' => '101', 'vigente' => true]);
    
    $deptB = \App\Models\Departamento::factory()->create([
        'region_id' => $regionB->id
    ]);

    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'distrital@example.com']);

    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'distrital@example.com',
        'email_verified_at' => now(),
        'estado' => 'vinculacion_pendiente'
    ]);
    $user->assignRole('jefe_distrital');
    \App\Models\DistritoUsuario::create([
        'usuario_id' => $user->id,
        'departamento_id' => $deptB->id
    ]);

    // Performer is jefe regional of region A
    $performer = Usuario::factory()->create();
    $performer->assignRole('jefe_regional');
    \App\Models\RegionUsuario::create([
        'usuario_id' => $performer->id,
        'region_id' => $regionA->id
    ]);

    $response = $this->actingAs($performer, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertStatus(403);
});

test('jefe distrital can confirm vinculation of conduccion in their district', function () {
    $dept = \App\Models\Departamento::factory()->create();
    $school = \App\Models\Escuela::factory()->create([
        'localidad_id' => \App\Models\Localidad::factory()->create(['departamento_id' => $dept->id])->id
    ]);

    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'director@example.com']);

    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'director@example.com',
        'email_verified_at' => now(),
        'estado' => 'vinculacion_pendiente'
    ]);

    // Crear CUPOF y movimiento para la persona en una escuela del distrito
    $cupof = \App\Models\Cupof::factory()->create([
        'escuela_id' => $school->id,
        'nombre_cargo' => 'DIRECTOR/A',
        'escalafon_id' => 1, // Docente
        'puesto_tipo_id' => 1 // Cargo
    ]);
    
    \App\Models\CupofMovimiento::create([
        'cupof_id' => $cupof->id,
        'persona_id' => $persona->id,
        'situacion_revista' => 'titular',
        'fecha_inicio' => now(),
        'activo' => true
    ]);

    // Performer is jefe distrital of dept
    $performer = Usuario::factory()->create();
    $performer->assignRole('jefe_distrital');
    \App\Models\DistritoUsuario::create([
        'usuario_id' => $performer->id,
        'departamento_id' => $dept->id
    ]);

    $response = $this->actingAs($performer, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertOk();
    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
});

test('conduccion role cannot confirm vinculation of anyone', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'test@example.com']);

    // Admin with director role
    $performer = Usuario::factory()->create();
    $performer->assignRole('director');

    $response = $this->actingAs($performer, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertStatus(403);
});
