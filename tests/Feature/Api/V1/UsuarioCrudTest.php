<?php

namespace Tests\Feature\Api\V1;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioCrudTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles and permissions are seeded
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        // Create admin user and assign role
        $this->adminUser = Usuario::factory()->create(['es_administrador' => true]);
        $this->adminUser->assignRole('admin_full');
        $this->actingAs($this->adminUser, 'sanctum');
    }

    public function testCanGetAListOfUsers(): void
    {
        Usuario::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/usuarios');
        // dd($response->json()); // Debug line to inspect response structure

        $response->assertOk()
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'nombre', 'documento_tipo_id', 'documento_numero', 'email']
                     ],
                     'meta' => [
                         'current_page', 'from', 'last_page', 'per_page', 'to', 'total'
                     ],
                     'links' => [
                         'first', 'last', 'prev', 'next'
                     ]
                 ])
                 ->assertJsonCount(8, 'data'); // 5 created + 3 from seeder
    }

    public function testCanCreateANewUser(): void
    {
        $userData = [
            'nombre' => 'New',
            'documento_tipo_id' => 1,
            'documento_numero' => '12345678',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/usuarios', $userData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Usuario creado con éxito.',
                     'user' => [
                         'id' => $response->json('user.id'), // Get the generated ID
                         'nombre' => 'New',
                         'documento_tipo_id' => 1,
                         'documento_numero' => '12345678',
                         'email' => 'newuser@example.com',
                     ],
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'email' => 'newuser@example.com',
            'documento_numero' => '12345678',
        ]);
    }

    public function testCannotCreateUserWithInvalidData(): void
    {
        $userData = [
            'nombre' => '', // Invalid
            'email' => 'invalid-email', // Invalid
        ];

        $response = $this->postJson('/api/v1/usuarios', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nombre', 'email']);
    }

    public function testCanGetASpecificUser(): void
    {
        $user = Usuario::factory()->create();

        $response = $this->getJson('/api/v1/usuarios/' . $user->id);

        $response->assertOk()
                 ->assertJson([
                     'data' => [
                         'id' => $user->id,
                         'nombre' => $user->nombre,
                         'documento_numero' => $user->documento_numero,
                         'email' => $user->email,
                     ]
                 ]);
    }

    public function testCanUpdateAUser(): void
    {
        $user = Usuario::factory()->create();
        $updatedData = [
            'nombre' => 'Updated Name',
            'documento_tipo_id' => 2,
            'documento_numero' => '87654321',
            'email' => 'updated.user@example.com',
        ];

        $response = $this->putJson('/api/v1/usuarios/' . $user->id, $updatedData);

        $response->assertOk()
                 ->assertJson([
                     'message' => 'Usuario actualizado con éxito.',
                     'user' => [
                         'id' => $user->id,
                         'nombre' => 'Updated Name',
                         'documento_tipo_id' => 2,
                         'documento_numero' => '87654321',
                         'email' => 'updated.user@example.com',
                     ],
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'id' => $user->id,
            'nombre' => 'Updated Name',
            'documento_numero' => '87654321',
            'email' => 'updated.user@example.com',
        ]);
    }

    public function testCannotUpdateUserWithDuplicateEmail(): void
    {
        $user1 = Usuario::factory()->create();
        $user2 = Usuario::factory()->create(); // This user's email will conflict

        $updatedData = [
            'nombre' => 'Updated Name',
            'apellido' => 'Updated Lastname',
            'email' => $user2->email, // Duplicate email
        ];

        $response = $this->putJson('/api/v1/usuarios/' . $user1->id, $updatedData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function testCanDeleteAUser(): void
    {
        $user = Usuario::factory()->create();

        $response = $this->deleteJson('/api/v1/usuarios/' . $user->id);

        $response->assertOk()
                 ->assertJson(['message' => 'Usuario eliminado con éxito.']);

        $this->assertSoftDeleted('usuarios', ['id' => $user->id]);
    }
}

