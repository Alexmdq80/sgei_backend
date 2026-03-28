<?php

namespace Tests\Feature\Api\V1;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RBACAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $superUser;
    protected Usuario $adminFullUser;
    protected Usuario $adminStandardUser;
    protected Usuario $regularUser; // A user without specific admin roles

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles and permissions are seeded
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        // Create test users and assign roles
        $this->superUser = Usuario::factory()->create([
            'email' => 'superuser@test.com',
            'es_administrador' => true,
        ]);
        $this->superUser->assignRole('superuser');

        $this->adminFullUser = Usuario::factory()->create([
            'email' => 'adminfull@test.com',
            'es_administrador' => true,
        ]);
        $this->adminFullUser->assignRole('admin_full');

        $this->adminStandardUser = Usuario::factory()->create([
            'email' => 'adminstandard@test.com',
            'es_administrador' => true,
        ]);
        $this->adminStandardUser->assignRole('admin_standard');

        $this->regularUser = Usuario::factory()->create(['password' => Hash::make('password')]);
    }

    // --- Profile Controller Tests (Autogestión) ---

    public function testAuthenticatedUserCanAccessTheirOwnProfile(): void
    {
        $this->actingAs($this->regularUser, 'sanctum');
        $response = $this->getJson('/api/v1/auth/me');
        $response->assertOk()
                 ->assertJson(['user' => ['id' => $this->regularUser->id]]);
    }

    public function testUnauthenticatedUserCannotAccessProfileRoutes(): void
    {
        $response = $this->getJson('/api/v1/auth/me');
        $response->assertStatus(401); // Unauthorized
    }

    // --- UsuarioController (Admin CRUD) Tests ---

    public function testSuperUserCanManageUsers(): void
    {
        $this->actingAs($this->superUser, 'sanctum');
        $response = $this->getJson('/api/v1/usuarios');
        $response->assertOk(); // Should be able to list users
    }

    public function testAdminFullUserCanManageUsers(): void
    {
        $this->actingAs($this->adminFullUser, 'sanctum');
        $response = $this->getJson('/api/v1/usuarios');
        $response->assertOk(); // Should be able to list users
    }

    public function testAdminStandardUserCannotManageUsers(): void
    {
        $this->actingAs($this->adminStandardUser, 'sanctum');
        $response = $this->getJson('/api/v1/usuarios');
        $response->assertStatus(403); // Forbidden
    }

    public function testRegularUserCannotManageUsers(): void
    {
        $this->actingAs($this->regularUser, 'sanctum');
        $response = $this->getJson('/api/v1/usuarios');
        $response->assertStatus(403); // Forbidden
    }

    public function testAdminFullUserCanCreateUser(): void
    {
        $this->actingAs($this->adminFullUser, 'sanctum');
        $userData = [
            'nombre' => 'Creator',
            'documento_tipo_id' => 1,
            'documento_numero' => '10101010',
            'email' => 'creator@example.com',
            'password' => 'password123',
        ];
        $response = $this->postJson('/api/v1/usuarios', $userData);
        $response->assertStatus(201);
    }

    public function testAdminFullUserCanUpdateUser(): void
    {
        $userToUpdate = Usuario::factory()->create();
        $this->actingAs($this->adminFullUser, 'sanctum');
        $updatedData = [
            'nombre' => 'Updated Admin',
            'documento_tipo_id' => 1,
            'documento_numero' => '20202020',
            'email' => 'updated.admin@example.com',
        ];
        $response = $this->putJson('/api/v1/usuarios/' . $userToUpdate->id, $updatedData);
        $response->assertOk();
    }

    public function testAdminFullUserCanDeleteUser(): void
    {
        $userToDelete = Usuario::factory()->create();
        $this->actingAs($this->adminFullUser, 'sanctum');
        $response = $this->deleteJson('/api/v1/usuarios/' . $userToDelete->id);
        $response->assertOk();
        $this->assertSoftDeleted('usuarios', ['id' => $userToDelete->id]);
    }
}
