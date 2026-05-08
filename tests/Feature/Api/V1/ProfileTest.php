<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->user = Usuario::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});

test('can get authenticated user profile', function () {
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
});

test('can update authenticated user profile', function () {
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
});

test('cannot update profile with invalid data', function () {
    $newData = [
        'nombre' => '', // Invalid
        'email' => 'invalid-email', // Invalid
    ];

    $response = $this->putJson('/api/v1/auth/profile', $newData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['nombre', 'email']);
});

test('cannot update profile with duplicate email', function () {
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
});

test('can update authenticated user avatar', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson('/api/v1/auth/avatar', ['avatar' => $file]);

    $response->assertOk()
             ->assertJsonStructure(['message', 'avatar_url']);

    // Check if user's avatar_path was updated in the database and file exists
    $this->user->refresh();
    expect($this->user->avatar_path)->not->toBeNull();
    
    // The filename should start with user_id_ and end with .jpg
    expect($this->user->avatar_path)->toStartWith('avatars/' . $this->user->id . '_');
    expect($this->user->avatar_path)->toEndWith('.jpg');
    
    Storage::disk('public')->assertExists($this->user->avatar_path);
});

test('old avatar is deleted when updating a new one', function () {
    Storage::fake('public');

    // First avatar
    $firstFile = UploadedFile::fake()->image('first.jpg');
    $this->postJson('/api/v1/auth/avatar', ['avatar' => $firstFile]);
    $this->user->refresh();
    $oldAvatarPath = $this->user->avatar_path;
    Storage::disk('public')->assertExists($oldAvatarPath);

    // Wait a second to ensure a different timestamp if needed
    sleep(1);

    // Second avatar
    $secondFile = UploadedFile::fake()->image('second.png');
    $this->postJson('/api/v1/auth/avatar', ['avatar' => $secondFile]);

    // Old avatar should be deleted
    Storage::disk('public')->assertMissing($oldAvatarPath);
    
    $this->user->refresh();
    Storage::disk('public')->assertExists($this->user->avatar_path);
    expect($this->user->avatar_path)->toStartWith('avatars/' . $this->user->id . '_');
    expect($this->user->avatar_path)->toEndWith('.png');
});

test('cannot upload invalid avatar file', function () {
    Storage::fake('public');

    $invalidFile = UploadedFile::fake()->create('document.pdf'); // Not an image

    $response = $this->postJson('/api/v1/auth/avatar', ['avatar' => $invalidFile]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['avatar']);
});

test('can update authenticated user password', function () {
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

    expect(password_verify($newPassword, $this->user->fresh()->password))->toBeTrue();
});

test('cannot update password with incorrect current password', function () {
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

    $response->assertStatus(400)
             ->assertJson([
                 'error' => 'La contraseña actual es incorrecta.',
                 'code' => 400
             ]);
});

test('cannot update password with invalid new password', function () {
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
});
