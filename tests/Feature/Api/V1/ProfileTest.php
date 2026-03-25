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
                         'email' => $this->user->email,
                     ]
                 ]);
    }

    public function testCanUpdateAuthenticatedUserProfile(): void
    {
        $newData = [
            'nombre' => 'Jane',
            'apellido' => 'Doe Updated',
            'email' => 'jane.doe.updated@example.com',
        ];

        $response = $this->putJson('/api/v1/auth/profile', $newData);

        $response->assertOk()
                 ->assertJson([
                     'message' => 'Perfil actualizado con éxito.',
                     'user' => [
                         'nombre' => 'Jane',
                         'apellido' => 'Doe Updated',
                         'email' => 'jane.doe.updated@example.com',
                     ],
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'id' => $this->user->id,
            'nombre' => 'Jane',
            'apellido' => 'Doe Updated',
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
            'apellido' => 'Doe Updated',
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

        // Check if the file was stored
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());

        // Check if user's avatar_path was updated in the database
        $this->user->refresh();
        $this->assertNotNull($this->user->avatar_path);
        $this->assertStringContainsString('avatars/' . $file->hashName(), $this->user->avatar_path);
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

        // Second avatar
        $secondFile = UploadedFile::fake()->image('second.png');
        $this->postJson('/api/v1/auth/avatar', ['avatar' => $secondFile]);

        // Old avatar should be deleted
        Storage::disk('public')->assertMissing($oldAvatarPath);
        Storage::disk('public')->assertExists('avatars/' . $secondFile->hashName());
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
        $oldPassword = 'password';
        $newPassword = 'new-secure-password';

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
        $oldPassword = 'password';
        $wrongPassword = 'wrong-password';
        $newPassword = 'new-secure-password';

        $this->user->password = bcrypt($oldPassword);
        $this->user->save();

        $response = $this->putJson('/api/v1/auth/password', [
            'current_password' => $wrongPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'error' => 'La contraseña actual es incorrecta.',
                     'code' => 422
                 ]);
    }

    public function testCannotUpdatePasswordWithInvalidNewPassword(): void
    {
        $oldPassword = 'password';
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
