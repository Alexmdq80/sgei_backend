<?php

use App\Models\DocumentoTipo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register with valid data', function () {
    $documentoTipo = DocumentoTipo::factory()->create();

    $response = $this->postJson('/api/v1/auth/register', [
        'nombre' => 'Test User',
        'email' => 'test@example.com',
        'documento_tipo_id' => $documentoTipo->id,
        'documento_numero' => '12345678',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'nombre',
                'email',
                'documento_tipo_id',
                'documento_numero',
            ]
        ]);

    $this->assertDatabaseHas('usuarios', [
        'email' => 'test@example.com',
        'documento_numero' => '12345678',
        'estado' => 'email_pendiente',
        'es_administrador' => false,
    ]);
});

test('registration fails with invalid email', function () {
    $documentoTipo = DocumentoTipo::factory()->create();

    $response = $this->postJson('/api/v1/auth/register', [
        'nombre' => 'Test User',
        'email' => 'invalid-email',
        'documento_tipo_id' => $documentoTipo->id,
        'documento_numero' => '12345678',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration fails if email already exists', function () {
    $existingUser = Usuario::factory()->create([
        'email' => 'existing@example.com'
    ]);

    $documentoTipo = DocumentoTipo::factory()->create();

    $response = $this->postJson('/api/v1/auth/register', [
        'nombre' => 'Test User',
        'email' => 'existing@example.com',
        'documento_tipo_id' => $documentoTipo->id,
        'documento_numero' => '12345678',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration fails if password confirmation does not match', function () {
    $documentoTipo = DocumentoTipo::factory()->create();

    $response = $this->postJson('/api/v1/auth/register', [
        'nombre' => 'Test User',
        'email' => 'test@example.com',
        'documento_tipo_id' => $documentoTipo->id,
        'documento_numero' => '12345678',
        'password' => 'Password123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
