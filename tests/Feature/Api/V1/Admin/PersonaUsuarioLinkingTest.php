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

test('un usuario sin permiso sistema.usuarios no puede confirmar vinculacion manualmente', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'test@example.com'
    ]);

    // Profesor no posee permiso 'sistema.usuarios' delegado
    $admin = Usuario::factory()->create(['es_administrador' => false]);
    $admin->assignRole('profesor');

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

test('unlinking user from persona revokes all roles except superuser and removes geographical contexts', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'test@example.com']);

    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'test@example.com',
        'estado' => 'activo'
    ]);
    $persona->update(['usuario_id' => $user->id]);

        $user->assignRole(['director', 'superuser']);

    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');

    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/unlink-user");

    $response->assertOk();
        $user->refresh();

    $this->assertNull($persona->fresh()->usuario_id, 'La persona debe quedar desvinculada');
    $this->assertFalse($user->hasRole('director'), 'El rol institucional debe revocarse');
    $this->assertTrue($user->hasRole('superuser'), 'El rol superuser debe preservarse');
});

// =========================================================================
// CONFIRMACIÓN DE VINCULACIÓN CON FORCE (EMAIL NO VERIFICADO)
// =========================================================================

test('confirm vinculation is blocked for unverified user without force', function () {
    // 1. Setup persona sin usuario vinculado
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'test@example.com']);

    // 2. Setup usuario sin email verificado
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'test@example.com',
        'estado' => 'vinculacion_pendiente'
    ]);

    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');

    // 3. Intentar confirmar sin force
    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/usuarios/{$user->id}/confirm-persona");

    $response->assertStatus(422);
    $this->assertNull($persona->fresh()->usuario_id, 'Persona should NOT be linked');
    $this->assertNull($user->fresh()->email_verified_at, 'Email should remain unverified');
});

test('confirm vinculation succeeds for unverified user with force', function () {
    // 1. Setup persona sin usuario vinculado
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'test@example.com']);

    // 2. Setup usuario sin email verificado
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'test@example.com',
        'estado' => 'vinculacion_pendiente'
    ]);

    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');

    // 3. Confirmar con force: true
    $response = $this->actingAs($admin, 'sanctum')
                     ->postJson("/api/v1/admin/usuarios/{$user->id}/confirm-persona", [
                        'force' => true
                     ]);

    $response->assertOk();
    $this->assertEquals($user->id, $persona->fresh()->usuario_id, 'Persona should be linked');
    $this->assertEquals('activo', $user->fresh()->estado, 'User should be active');
    $this->assertNull($user->fresh()->email_verified_at, 'Email should remain unverified even after force link');
});
