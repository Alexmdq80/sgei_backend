<?php

use App\Models\Usuario;
use App\Models\Escuela;
use App\Models\EscuelaUsuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seeders necesarios
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    
    // Limpiar caché de permisos de Spatie
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    // Crear administrador con rol superuser y permisos
    $this->admin = Usuario::factory()->create();
    $this->admin->assignRole('superuser');
    $this->admin->givePermissionTo('sistema.usuarios');

    // Crear usuario normal sin permisos
    $this->standardUser = Usuario::factory()->create();
});

test('unauthorized users cannot access school-user links', function () {
    // No autenticado
    $this->getJson('/api/v1/admin/escuela-usuarios')
         ->assertStatus(401);

    // Autenticado sin permiso
    $this->actingAs($this->standardUser, 'sanctum')
         ->getJson('/api/v1/admin/escuela-usuarios')
         ->assertStatus(403);
});

test('admin can list all institutional links', function () {
    $roleId = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    EscuelaUsuario::factory()->count(5)->create(['role_id' => $roleId, 'verified_at' => now()]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/escuela-usuarios');

    $response->assertStatus(200)
             ->assertJsonCount(5, 'data');
});

test('superuser can assign any role to any user in any school', function () {
    $escuela = Escuela::factory()->create();
    $user = Usuario::factory()->create(['estado' => 'email_verificado']);
    $role = \Spatie\Permission\Models\Role::where('name', 'profesor')->first();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-usuarios", [
                         'usuario_id' => $user->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $role->id
                     ]);

    $response->assertStatus(201)
             ->assertJsonPath('message', 'Rol institucional asignado con éxito.');

    $this->assertDatabaseHas('escuela_usuario', [
        'usuario_id' => $user->id,
        'escuela_id' => $escuela->id,
        'role_id' => $role->id,
        'verified_at' => now()->toDateTimeString()
    ]);

    $this->assertDatabaseHas('usuarios', [
        'id' => $user->id,
        'estado' => 'activo'
    ]);
});

test('jefe distrital can assign hierarchical roles globally', function () {
    $jefe = Usuario::factory()->create();
    $jefe->assignRole('jefe_distrital');
    $jefe->givePermissionTo('sistema.usuarios');

    $escuela = Escuela::factory()->create();
    $user = Usuario::factory()->create(['estado' => 'email_verificado']);
    $role = \Spatie\Permission\Models\Role::where('name', 'director')->first();

    $response = $this->actingAs($jefe, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-usuarios", [
                         'usuario_id' => $user->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $role->id
                     ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('escuela_usuario', [
        'usuario_id' => $user->id,
        'escuela_id' => $escuela->id,
        'role_id' => $role->id
    ]);
});

test('local admin cannot assign hierarchical roles', function () {
    $director = Usuario::factory()->create();
    $directorRole = \Spatie\Permission\Models\Role::where('name', 'director')->first();
    $escuela = Escuela::factory()->create();
    
    // Vincular al director a su escuela
    EscuelaUsuario::create([
        'usuario_id' => $director->id,
        'escuela_id' => $escuela->id,
        'role_id' => $directorRole->id,
        'verified_at' => now()
    ]);
    $director->givePermissionTo('sistema.usuarios');

    $user = Usuario::factory()->create(['estado' => 'email_verificado']);
    $targetRole = \Spatie\Permission\Models\Role::where('name', 'vicedirector')->first(); // Jerárquico

    $response = $this->actingAs($director, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-usuarios", [
                         'usuario_id' => $user->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $targetRole->id
                     ]);

    $response->assertStatus(403)
             ->assertJsonPath('error', 'No tienes permisos para asignar roles jerárquicos.');
});

test('local admin can assign non-hierarchical roles to their school', function () {
    $director = Usuario::factory()->create();
    $directorRole = \Spatie\Permission\Models\Role::where('name', 'director')->first();
    $escuela = Escuela::factory()->create();
    
    // Vincular al director a su escuela
    EscuelaUsuario::create([
        'usuario_id' => $director->id,
        'escuela_id' => $escuela->id,
        'role_id' => $directorRole->id,
        'verified_at' => now()
    ]);
    $director->givePermissionTo('sistema.usuarios');

    $user = Usuario::factory()->create(['estado' => 'email_verificado']);
    $targetRole = \Spatie\Permission\Models\Role::where('name', 'profesor')->first(); // No jerárquico

    $response = $this->actingAs($director, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-usuarios", [
                         'usuario_id' => $user->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $targetRole->id
                     ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('escuela_usuario', [
        'usuario_id' => $user->id,
        'escuela_id' => $escuela->id,
        'role_id' => $targetRole->id
    ]);
});
