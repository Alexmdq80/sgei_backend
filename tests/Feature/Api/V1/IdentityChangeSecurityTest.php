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

test('user is unlinked from persona and set to email_pendiente when changing email to a non-matching one', function () {
    // 1. Setup linked user and persona
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'old@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'usuario_id' => $user->id
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'old@example.com'
    ]);

    $this->assertEquals($user->id, $persona->fresh()->usuario_id);

    // 2. Change email via UserService
    $userService = app(UserService::class);
    $userService->updateProfile($user, ['email' => 'new@example.com', 'nombre' => $user->nombre]);

    // 3. Verify unlinking and status
    $this->assertNull($persona->fresh()->usuario_id, 'Persona should be unlinked from user');
    $this->assertNull($user->fresh()->email_verified_at, 'Email should be unverified');
    $this->assertEquals('email_pendiente', $user->fresh()->estado, 'Status should be email_pendiente');
});

test('user is unlinked and set to vinculacion_pendiente when changing email to one that matches an unlinked persona', function () {
    // 1. Setup linked user and persona A (current link)
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '11223344',
        'email' => 'current@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    $personaA = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '11223344',
        'usuario_id' => $user->id
    ]);
    
    Contacto::create(['persona_id' => $personaA->id, 'email' => 'current@example.com']);

    // 2. Setup Persona B with the NEW email and SAME DNI (unlinked)
    // Note: In a real scenario, DNI is unique, so this would be the same person 
    // but maybe the record was "recreated" or we are simulating the match.
    // Actually, let's use the same Persona record for a more realistic test:
    // User changes email to 'new@example.com'. 
    // If we update the Persona Contact first (e.g. by an admin), then the User updates their email.
    
    $personaA->contacto->update(['email' => 'new@example.com']);
    $personaA->update(['usuario_id' => $user->id]); // Still linked to old identity logic

    // 3. Change user email to the one now in Persona record
    $userService = app(UserService::class);
    $userService->updateProfile($user, ['email' => 'new@example.com', 'nombre' => $user->nombre]);

    // 4. Verify results
    $this->assertNull($personaA->fresh()->usuario_id, 'Persona A should be unlinked during email change');
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado, 'User status should be vinculacion_pendiente because new email matches persona');
    $this->assertNull($user->fresh()->email_verified_at, 'Email must be unverified');
});

test('user remains in vinculacion_pendiente after verifying email if match exists', function () {
    // 1. Setup user in vinculacion_pendiente (unverified)
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'juan@example.com',
        'estado' => 'vinculacion_pendiente'
    ]);

    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'juan@example.com']);

    // 2. Mark as verified
    $user->markEmailAsVerified();

    // 3. Should still be pending confirmation
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado, 'Should still be pending admin confirmation after verification');
    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('user is unlinked and set to vinculacion_pendiente when changing DNI to one that matches a persona', function () {
    // 1. Setup user (already verified)
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '11111111',
        'email' => 'juan@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    // Persona currently linked to user
    $personaA = Persona::factory()->create(['documento_tipo_id' => 1, 'documento_numero' => '11111111', 'usuario_id' => $user->id]);
    Contacto::create(['persona_id' => $personaA->id, 'email' => 'juan@example.com']);

    // 2. Setup Persona B (matching the FUTURE DNI of the user)
    // IMPORTANT: It MUST have the SAME email as the user for the link matching logic to work
    // Since we have a unique email constraint, we MUST unlink PersonaA first or change its email.
    
    $personaA->contacto->update(['email' => 'old_email@example.com']); // Free 'juan@example.com'
    
    $personaB = Persona::factory()->create(['documento_tipo_id' => 1, 'documento_numero' => '22222222']);
    Contacto::create(['persona_id' => $personaB->id, 'email' => 'juan@example.com']);

    // 3. Change user DNI via UserService
    $userService = app(UserService::class);
    $userService->updateProfile($user, ['documento_numero' => '22222222', 'nombre' => $user->nombre]);

    // 4. Verify results
    $this->assertNull($personaA->fresh()->usuario_id, 'Original Persona should be unlinked');
    $this->assertNull($personaB->fresh()->usuario_id, 'New matching Persona should NOT be linked automatically (must be pending)');
    $this->assertEquals('vinculacion_pendiente', $user->fresh()->estado, 'User status should be vinculacion_pendiente');
});

test('persona is unlinked from user when persona DNI is modified', function () {
    // 1. Setup linked user and persona
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '33333333',
        'email' => 'pedro@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '33333333',
        'usuario_id' => $user->id
    ]);
    Contacto::create(['persona_id' => $persona->id, 'email' => 'pedro@example.com']);

    // 2. Modify Persona DNI via Controller
    $admin = Usuario::factory()->create(['es_administrador' => true])->assignRole('superuser');
    
    $response = $this->actingAs($admin, 'sanctum')
                     ->putJson("/api/v1/admin/personas/{$persona->id}", [
                        'apellido' => $persona->apellido,
                        'nombre' => $persona->nombre,
                        'documento_tipo_id' => 1,
                        'documento_numero' => '44444444', // Changed
                        'email' => 'pedro@example.com'
                     ]);

    $response->assertOk();

    // 3. Verify unlinking
    $this->assertNull($persona->fresh()->usuario_id, 'Persona should be unlinked from user because DNI changed');
    $this->assertEquals('email_verificado', $user->fresh()->estado, 'User should return to email_verificado status (no longer active link)');
});
