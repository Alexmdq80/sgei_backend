<?php

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Contacto;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
});

test('persona is linked to user when email is verified and email matches', function () {
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

    // 3. Verify email (triggers linkToPersona via markEmailAsVerified override in Usuario model)
    $user->markEmailAsVerified();

    // 4. Check if linked
    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
});

test('persona is not linked if email does not match', function () {
    // 1. Create a User
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'juan@example.com'
    ]);

    // 2. Create a Persona with matching identification but DIFFERENT email in Contacto
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'pedro@example.com'
    ]);

    $user->markEmailAsVerified();

    // 4. Check that it is NOT linked
    $this->assertNull($persona->fresh()->usuario_id);
});

test('persona is not linked if no contacto record exists', function () {
    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'juan@example.com'
    ]);

    // Persona exists but has no Contacto record
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
    ]);

    $user->markEmailAsVerified();

    $this->assertNull($persona->fresh()->usuario_id);
});

test('user is linked to existing persona when persona is created with matching email', function () {
    // 1. Create an existing verified User
    $user = Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'juan@example.com',
        'email_verified_at' => now()
    ]);

    // 2. Create a Persona with matching identification
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
    ]);

    // 3. Create Contacto with matching email (should trigger link via ContactoObserver)
    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'juan@example.com'
    ]);

    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
});

test('persona is not linked if identification does not match', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '11111111'
    ]);

    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '22222222'
    ]);

    $user->markEmailAsVerified();

    $this->assertNull($persona->fresh()->usuario_id);
});

test('persona is not linked if email not verified', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    $user = Usuario::factory()->unverified()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    // Trigger link attempt
    app(UserService::class)->linkToPersona($user);

    $this->assertNull($persona->fresh()->usuario_id);
});

test('admin created verified user links immediately', function () {
    $persona = Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ]);

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'admin_verified@example.com'
    ]);

    // Use UserService to create a verified user
    $userData = [
        'nombre' => 'Admin User',
        'email' => 'admin_verified@example.com',
        'password' => 'password123',
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email_verified_at' => now(), // Already verified
    ];

    $user = app(UserService::class)->create($userData);

    $this->assertEquals($user->id, $persona->fresh()->usuario_id);
});
