<?php

namespace Tests\Feature\Api\V1;

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonaUsuarioLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    }

    public function testPersonaIsLinkedToUserWhenEmailIsVerified(): void
    {
        // 1. Create a Persona
        $persona = Persona::factory()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678',
            'nombre' => 'Juan',
            'apellido' => 'Perez'
        ]);

        // 2. Create an unverified User with same identification
        $user = Usuario::factory()->unverified()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678'
        ]);

        $this->assertNull($persona->fresh()->usuario_id);

        // 3. Verify email
        $user->markEmailAsVerified();

        // 4. Check if linked
        $this->assertEquals($user->id, $persona->fresh()->usuario_id);
        $this->assertEquals($persona->id, $user->fresh()->persona->id);
    }

    public function testPersonaIsNotLinkedIfIdentificationDoesNotMatch(): void
    {
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
    }

    public function testPersonaIsNotLinkedIfEmailNotVerified(): void
    {
        $persona = Persona::factory()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678'
        ]);

        $user = Usuario::factory()->unverified()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678'
        ]);

        // Trigger link attempt (though UserService create already does it internally if verified)
        app(\App\Services\UserService::class)->linkToPersona($user);

        $this->assertNull($persona->fresh()->usuario_id);
    }

    public function testAdminCreatedVerifiedUserLinksImmediately(): void
    {
        $persona = Persona::factory()->create([
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678'
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

        $user = app(\App\Services\UserService::class)->create($userData);

        $this->assertEquals($user->id, $persona->fresh()->usuario_id);
    }
}
