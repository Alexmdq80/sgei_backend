<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    }

    /**
     * Test successful login with email.
     */
    public function test_user_can_login_with_correct_credentials(): void
    {
        $password = 'secret-password';
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
    }

    /**
     * Test successful login with document.
     */
    public function test_user_can_login_with_document_credentials(): void
    {
        $password = 'secret-password';
        $usuario = Usuario::factory()->create([
            'documento_tipo_id' => 1, // DNI
            'documento_numero' => '12345678',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678',
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
            ->assertJsonPath('user.documento_numero', '12345678');
    }

    /**
     * Test login fails with incorrect document.
     */
    public function test_user_cannot_login_with_incorrect_document_number(): void
    {
        $password = 'secret-password';
        $usuario = Usuario::factory()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'documento_tipo_id' => 1,
            'documento_numero' => '87654321', // Wrong number
            'password' => $password,
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Las credenciales proporcionadas son incorrectas.',
                'code' => 401
            ]);

        $this->assertGuest();
    }

    /**
     * Test login fails with unverified email using document.
     */
    public function test_user_cannot_login_with_document_if_email_is_unverified(): void
    {
        $password = 'secret-password';
        $usuario = Usuario::factory()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678',
            'password' => Hash::make($password),
            'email_verified_at' => null, // Not verified
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678',
            'password' => $password,
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Debes verificar tu correo electrónico antes de iniciar sesión.',
                'code' => 401
            ]);
    }

    /**
     * Test login fails with incorrect password.
     */
    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $usuario = Usuario::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct-password'),
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
    }

    /**
     * Test login fails with non-existent email.
     */
    public function test_user_cannot_login_with_non_existent_email(): void
    {
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
    }

    /**
     * Test login validation errors.
     */
    public function test_login_requires_email_or_document_and_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'documento_tipo_id', 'documento_numero', 'password']);
    }

    /**
     * Test user can logout.
     */
    public function test_user_can_logout(): void
    {
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sesión cerrada correctamente.',
                'code' => 200
            ]);

        $this->assertCount(0, $usuario->tokens);
    }
}
