<?php

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Escuela;
use App\Models\EscuelaPersona;
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
    $this->getJson('/api/v1/admin/escuela-personas')
         ->assertStatus(401);

    // Autenticado sin permiso
    $this->actingAs($this->standardUser, 'sanctum')
         ->getJson('/api/v1/admin/escuela-personas')
         ->assertStatus(403);
});

test('admin can list all institutional links', function () {
    $roleId = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    EscuelaPersona::factory()->count(5)->create(['role_id' => $roleId, 'verified_at' => now()]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/escuela-personas');

    $response->assertStatus(200)
             ->assertJsonCount(5, 'data');
});

test('superuser cannot assign roles directly via institutional links', function () {
    $escuela = Escuela::factory()->create();
    $persona = Persona::factory()->create();
    $role = \Spatie\Permission\Models\Role::where('name', 'profesor')->first();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-personas", [
                         'persona_id' => $persona->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $role->id
                     ]);

    $response->assertStatus(403)
              ->assertJsonPath('error', 'No tienes autoridad (rol jerárquico) en esta institución para realizar asignaciones.');
});

test('superuser cannot assign superuser role to any user via institutional links', function () {
    $escuela = Escuela::factory()->create();
    $persona = Persona::factory()->create();
    $superuserRole = \Spatie\Permission\Models\Role::where('name', 'superuser')->first();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-personas", [
                         'persona_id' => $persona->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $superuserRole->id
                     ]);

    $response->assertStatus(403)
             ->assertJsonPath('error', 'El rol de Superusuario no puede ser asignado institucionalmente.');

    $this->assertDatabaseMissing('escuela_persona', [
        'persona_id' => $persona->id,
        'role_id' => $superuserRole->id
    ]);
});


test('local admin cannot assign hierarchical roles', function () {
    $director = Usuario::factory()->create();
    $personaDirector = Persona::factory()->create(['usuario_id' => $director->id]);
    $directorRole = \Spatie\Permission\Models\Role::where('name', 'director')->first();
    $escuela = Escuela::factory()->create();
    
    // Vincular al director a su escuela
    EscuelaPersona::create([
        'persona_id' => $personaDirector->id,
        'escuela_id' => $escuela->id,
        'role_id' => $directorRole->id,
        'verified_at' => now()
    ]);
    $director->givePermissionTo('sistema.usuarios');

    $targetPersona = Persona::factory()->create();
    $targetRole = \Spatie\Permission\Models\Role::where('name', 'vicedirector')->first(); // Jerárquico

    $response = $this->actingAs($director, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-personas", [
                         'persona_id' => $targetPersona->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $targetRole->id
                     ]);

        $response->assertStatus(403)
             ->assertJsonPath('error', 'Esta acción está reservada para el Superusuario.');
});

test('local admin can assign non-hierarchical roles to their school', function () {
    $director = Usuario::factory()->create();
    $personaDirector = Persona::factory()->create(['usuario_id' => $director->id]);
    $directorRole = \Spatie\Permission\Models\Role::where('name', 'director')->first();
    $escuela = Escuela::factory()->create();
    
    // Vincular al director a su escuela
    EscuelaPersona::create([
        'persona_id' => $personaDirector->id,
        'escuela_id' => $escuela->id,
        'role_id' => $directorRole->id,
        'verified_at' => now()
    ]);
    $director->givePermissionTo('sistema.usuarios');

    $targetPersona = Persona::factory()->create();
    $targetRole = \Spatie\Permission\Models\Role::where('name', 'profesor')->first(); // No jerárquico

    $response = $this->actingAs($director, 'sanctum')
                     ->postJson("/api/v1/admin/escuela-personas", [
                         'persona_id' => $targetPersona->id,
                         'escuela_id' => $escuela->id,
                         'role_id' => $targetRole->id
                     ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('escuela_persona', [
        'persona_id' => $targetPersona->id,
        'escuela_id' => $escuela->id,
        'role_id' => $targetRole->id
    ]);
});


test('usuario sin autoridad no puede asignar roles institucionales', function () {
    $persona = Persona::factory()->create();
    $escuela = Escuela::factory()->create();
    $rol = \Spatie\Permission\Models\Role::where('name', 'profesor')->first();

    $response = $this->actingAs($this->standardUser, 'sanctum')
        ->postJson("/api/v1/admin/escuela-personas", [
            'persona_id' => $persona->id,
            'escuela_id' => $escuela->id,
            'role_id' => $rol->id
        ]);

    $response->assertStatus(403);
});
