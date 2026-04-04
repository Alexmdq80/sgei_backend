<?php

use App\Models\Usuario;
use App\Models\Escuela;
use App\Models\EscuelaUsuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seeders necesarios
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);

    // 1. Setup Admin
    $this->admin = Usuario::factory()->create([
        'es_administrador' => true,
        'email_verified_at' => now(),
    ]);
    $this->admin->givePermissionTo('sistema.usuarios');

    // 2. Setup Schools
    $this->escuelaA = Escuela::factory()->create(['nombre' => 'Escuela A', 'cue_anexo' => '111111100']);
    $this->escuelaB = Escuela::factory()->create(['nombre' => 'Escuela B', 'cue_anexo' => '222222200']);

    // 3. Setup Roles
    $rol = \Spatie\Permission\Models\Role::where('name', 'director')->first();

    // 4. Setup Users with different vinculations
    
    // User 1: Vinculated to Escuela A (Verified)
    $user1 = Usuario::factory()->create(['nombre' => 'User One']);
    EscuelaUsuario::create([
        'id' => Str::uuid(),
        'usuario_id' => $user1->id,
        'escuela_id' => $this->escuelaA->id,
        'role_id' => $rol->id,
        'verified_at' => now()
    ]);

    // User 2: Vinculated to Escuela A (Pending)
    $user2 = Usuario::factory()->create(['nombre' => 'User Two']);
    EscuelaUsuario::create([
        'id' => Str::uuid(),
        'usuario_id' => $user2->id,
        'escuela_id' => $this->escuelaA->id,
        'role_id' => $rol->id,
        'verified_at' => null
    ]);

    // User 3: Vinculated to Escuela B (Verified)
    $user3 = Usuario::factory()->create(['nombre' => 'User Three']);
    EscuelaUsuario::create([
        'id' => Str::uuid(),
        'usuario_id' => $user3->id,
        'escuela_id' => $this->escuelaB->id,
        'role_id' => $rol->id,
        'verified_at' => now()
    ]);

    // User 4: No vinculations
    Usuario::factory()->create(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios por escuela específica', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?escuela_id={$this->escuelaA->id}");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data') // User One and User Two
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonFragment(['nombre' => 'User Two'])
        ->assertJsonMissing(['nombre' => 'User Three'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios con vinculaciones ya aprobadas', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?vinculation=vinculated");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data') // User One and User Three
        ->assertJsonFragment(['nombre' => 'User One'])
        ->assertJsonFragment(['nombre' => 'User Three'])
        ->assertJsonMissing(['nombre' => 'User Two'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('el administrador puede filtrar usuarios con vinculaciones pendientes', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?vinculation=pending");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data') // Only User Two
        ->assertJsonFragment(['nombre' => 'User Two'])
        ->assertJsonMissing(['nombre' => 'User One'])
        ->assertJsonMissing(['nombre' => 'User Three'])
        ->assertJsonMissing(['nombre' => 'User Four']);
});

test('la respuesta de usuarios incluye el CUE y el nombre del rol escolar', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/usuarios?search=User One");

    $response->assertStatus(200)
        ->assertJsonPath('data.0.escuela_usuarios.0.escuela.cue_anexo', '111111100')
        ->assertJsonPath('data.0.escuela_usuarios.0.rol_escolar.nombre', 'Director');
});
