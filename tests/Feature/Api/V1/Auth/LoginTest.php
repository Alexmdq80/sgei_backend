<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Limpiar caché de permisos
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
});

test('user can login with correct credentials', function () {
    $password = 'Sgei!2026_Test';
    $usuario = Usuario::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'user@example.com',
        'password' => $password,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => [
                'id',
                'nombre',
                'email',
            ],
            'token',
        ])
        ->assertJsonPath('user.email', 'user@example.com');
});

test('user can login with document credentials', function () {
    $password = 'Sgei!2026_Test';
    $usuario = Usuario::factory()->create([
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'documento_tipo_id' => $usuario->documento_tipo_id,
        'documento_numero' => $usuario->documento_numero,
        'password' => $password,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => [
                'id',
                'nombre',
                'documento_numero',
            ],
            'token',
        ])
        ->assertJsonPath('user.documento_numero', $usuario->documento_numero);
});

test('user cannot login with incorrect document number', function () {
    $password = 'Sgei!2026_Test';
    $usuario = Usuario::factory()->create([
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'documento_tipo_id' => $usuario->documento_tipo_id,
        'documento_numero' => '87654321', // Wrong number (not the one in factory)
        'password' => $password,
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Las credenciales proporcionadas son incorrectas.',
            'code' => 401
        ]);

    $this->assertGuest();
});

test('user can login with document even if email is unverified', function () {
    $password = 'Sgei!2026_Test';
    $usuario = Usuario::factory()->create([
        'password' => Hash::make($password),
        'email_verified_at' => null, // Not verified
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'documento_tipo_id' => $usuario->documento_tipo_id,
        'documento_numero' => $usuario->documento_numero,
        'password' => $password,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'email', 'email_verified_at']
        ])
        ->assertJsonPath('user.email_verified_at', null);
});

test('user cannot login with incorrect password', function () {
    $usuario = Usuario::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('Sgei!2026_Test'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'user@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Las credenciales proporcionadas son incorrectas.',
            'code' => 401
        ]);

    $this->assertGuest();
});

test('user cannot login with non existent email', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Las credenciales proporcionadas son incorrectas.',
            'code' => 401
        ]);

    $this->assertGuest();
});

test('login requires email or document and password', function () {
    $response = $this->postJson('/api/v1/auth/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'documento_tipo_id', 'documento_numero', 'password']);
});

test('user can logout', function () {
    $usuario = Usuario::factory()->create();
    $token = $usuario->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Sesión cerrada correctamente.',
            'code' => 200
        ]);

    expect($usuario->tokens)->toHaveCount(0);
});
