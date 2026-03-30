<?php

namespace Tests\Feature\Api\V1;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
        $this->user = Usuario::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function testCanGetAuthenticatedUserProfile(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertOk()
                 ->assertJson([
                     'user' => [
                         'id' => $this->user->id,
                         'nombre' => $this->user->nombre,
                         'documento_numero' => $this->user->documento_numero,
                         'email' => $this->user->email,
                     ]
                 ]);
    }

    public function testCanUpdateAuthenticatedUserProfile(): void
    {
        $newData = [
            'nombre' => 'Jane',
            'documento_tipo_id' => 1,
            'documento_numero' => '98765432',
            'email' => 'jane.doe.updated@example.com',
        ];

        $response = $this->putJson('/api/v1/auth/profile', $newData);

        $response->assertOk()
                 ->assertJson([
                     'message' => 'Perfil actualizado con éxito.',
                     'user' => [
                         'nombre' => 'Jane',
                         'documento_tipo_id' => 1,
                         'documento_numero' => '98765432',
                         'email' => 'jane.doe.updated@example.com',
                     ],
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'id' => $this->user->id,
            'nombre' => 'Jane',
            'documento_numero' => '98765432',
            'email' => 'jane.doe.updated@example.com',
        ]);
    }

    public function testCannotUpdateProfileWithInvalidData(): void
    {
        $newData = [
            'nombre' => '', // Invalid
            'email' => 'invalid-email', // Invalid
        ];

        $response = $this->putJson('/api/v1/auth/profile', $newData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'email']);
    }

    public function testCannotUpdateProfileWithDuplicateEmail(): void
    {
        Usuario::factory()->create(['email' => 'other@example.com']);

        $newData = [
            'nombre' => 'Jane',
            'documento_tipo_id' => 1,
            'documento_numero' => '98765432',
            'email' => 'other@example.com',
        ];

        $response = $this->putJson('/api/v1/auth/profile', $newData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function testCanUpdateAuthenticatedUserAvatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/api/v1/auth/avatar', ['avatar' => $file]);

        $response->assertOk()
                 ->assertJsonStructure(['message', 'avatar_url']);

        // Check if user's avatar_path was updated in the database and file exists
        $this->user->refresh();
        $this->assertNotNull($this->user->avatar_path);
        
        // The filename should start with user_id_ and end with .jpg
        $this->assertStringStartsWith('avatars/' . $this->user->id . '_', $this->user->avatar_path);
        $this->assertStringEndsWith('.jpg', $this->user->avatar_path);
        
        Storage::disk('public')->assertExists($this->user->avatar_path);
    }

    public function testOldAvatarIsDeletedWhenUpdatingANewOne(): void
    {
        Storage::fake('public');

        // First avatar
        $firstFile = UploadedFile::fake()->image('first.jpg');
        $this->postJson('/api/v1/auth/avatar', ['avatar' => $firstFile]);
        $this->user->refresh();
        $oldAvatarPath = $this->user->avatar_path;
        Storage::disk('public')->assertExists($oldAvatarPath);

        // Wait a second to ensure a different timestamp if needed (though user_id is enough to distinguish from other users, same user needs timestamp)
        // Actually time() changes every second.
        sleep(1);

        // Second avatar
        $secondFile = UploadedFile::fake()->image('second.png');
        $this->postJson('/api/v1/auth/avatar', ['avatar' => $secondFile]);

        // Old avatar should be deleted
        Storage::disk('public')->assertMissing($oldAvatarPath);
        
        $this->user->refresh();
        Storage::disk('public')->assertExists($this->user->avatar_path);
        $this->assertStringStartsWith('avatars/' . $this->user->id . '_', $this->user->avatar_path);
        $this->assertStringEndsWith('.png', $this->user->avatar_path);
    }

    public function testCannotUploadInvalidAvatarFile(): void
    {
        Storage::fake('public');

        $invalidFile = UploadedFile::fake()->create('document.pdf'); // Not an image

        $response = $this->postJson('/api/v1/auth/avatar', ['avatar' => $invalidFile]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['avatar']);
    }

    public function testCanUpdateAuthenticatedUserPassword(): void
    {
        $oldPassword = 'Sgei!2026_Test';
        $newPassword = 'Sgei!2026_New';

        $this->user->password = bcrypt($oldPassword);
        $this->user->save();

        $response = $this->putJson('/api/v1/auth/password', [
            'current_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertOk()
                 ->assertJson(['message' => 'Contraseña actualizada con éxito.']);

        $this->assertTrue(password_verify($newPassword, $this->user->fresh()->password));
    }

    public function testCannotUpdatePasswordWithIncorrectCurrentPassword(): void
    {
        $oldPassword = 'Sgei!2026_Test';
        $wrongPassword = 'Wrong!Sgei2026';
        $newPassword = 'Sgei!2026_New';

        $this->user->password = bcrypt($oldPassword);
        $this->user->save();

        $response = $this->putJson('/api/v1/auth/password', [
            'current_password' => $wrongPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'error' => 'Error de validación.',
                     'errors' => [
                         'current_password' => ['La contraseña actual es incorrecta.']
                     ],
                     'code' => 422
                 ]);
    }

    public function testCannotUpdatePasswordWithInvalidNewPassword(): void
    {
        $oldPassword = 'Sgei!2026_Test';
        $newPassword = 'short'; // Invalid password

        $this->user->password = bcrypt($oldPassword);
        $this->user->save();

        $response = $this->putJson('/api/v1/auth/password', [
            'current_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }
}
