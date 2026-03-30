<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\DocumentoTipo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
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
    }

    public function test_registration_fails_with_invalid_email(): void
    {
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
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
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
    }

    public function test_registration_fails_if_password_confirmation_does_not_match(): void
    {
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
    }
}
