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

    // 4. Check if status is pending and NOT linked yet
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado);
    $this->assertNull($persona->fresh()->usuario_id);
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
    
    // NEW RULE: Should be pending confirmation even if verified
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado);
    $persona = Persona::where('documento_numero', '12345678')->first();
    $this->assertNull($persona->usuario_id, 'Should NOT link automatically anymore');
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
    
    // Check if status is pending and NOT linked
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado);
    $persona = Persona::where('documento_numero', '87654321')->first();
    $this->assertNull($persona->usuario_id);
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

    // Admin without appropriate role (supervisor_curricular has sistema.usuarios but not in allowedRoles)
    $admin = Usuario::factory()->create(['es_administrador' => true]);
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

test('conduccion role cannot confirm vinculation if persona has no relation to their school', function () {
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

    // Admin with director role but NOT linked to any school that persona is related to
    $admin = Usuario::factory()->create();
    $admin->assignRole('director');
    
    // Create a school and link admin to it
    $school = \App\Models\Escuela::factory()->create();
    \App\Models\EscuelaUsuario::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'escuela_id' => $school->id,
        'usuario_id' => $admin->id,
        'role_id' => \Spatie\Permission\Models\Role::where('name', 'director')->first()->id,
        'verified_at' => now()
    ]);

    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertStatus(403);
    $response->assertJsonPath('error', 'Restricción de Seguridad: El Equipo de Conducción solo puede confirmar vinculaciones de personas relacionadas con su propia institución (por CUPOF, inscripción o vínculo familiar).');
});

test('conduccion role can confirm vinculation if persona has relation (CUPOF) to their school', function () {
    // Asegurar datos maestros
    \App\Models\Escalafon::firstOrCreate(['id' => 1], ['nombre' => 'DOCENTE', 'vigente' => true]);
    \App\Models\PuestoTipo::firstOrCreate(['id' => 1], ['nombre' => 'CARGO', 'vigente' => true]);

    $school = \App\Models\Escuela::factory()->create();
    
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'test@example.com'
    ]);

    // Create a CUPOF relation for this persona in that school
    $cupof = \App\Models\Cupof::create([
        'codigo_cupof' => 'TEST-123',
        'escuela_id' => $school->id,
        'escalafon_id' => 1, // Default Docente for tests
        'puesto_tipo_id' => 1, // Default Cargo for tests
        'cantidad' => 1
    ]);
    \App\Models\CupofMovimiento::create([
        'cupof_id' => $cupof->id,
        'persona_id' => $persona->id,
        'activo' => true,
        'fecha_inicio' => now()
    ]);

    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'test@example.com',
        'email_verified_at' => now(),
        'estado' => 'vinculacion_pendiente'
    ]);

    // Admin with director role linked to THE SAME school
    $admin = Usuario::factory()->create();
    $admin->assignRole('director');
    
    \App\Models\EscuelaUsuario::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'escuela_id' => $school->id,
        'usuario_id' => $admin->id,
        'role_id' => \Spatie\Permission\Models\Role::where('name', 'director')->first()->id,
        'verified_at' => now()
    ]);

    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/link-user");

    $response->assertOk();
    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
});
