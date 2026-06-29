<?php

namespace Tests\Feature\Api\V1;

use App\Models\Plan;
use App\Models\Anio;
use App\Models\Usuario;
use App\Models\Ambito;
use App\Models\EscuelaUsuario;
use Spatie\Permission\Models\Role;
use Tests\ProvidesRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, ProvidesRoles::class);

beforeEach(function () {
    $this->seedRoles();
    
    // Create a superuser
    $this->superuser = Usuario::factory()->create(['estado' => 'activo']);
    $this->superuser->assignRole('superuser');

    // Create different users with restricted roles
    $this->jefeProvincial = Usuario::factory()->create(['estado' => 'activo']);
    $this->jefeProvincial->assignRole('jefe_provincial');

    $this->jefeRegional = Usuario::factory()->create(['estado' => 'activo']);
    $this->jefeRegional->assignRole('jefe_regional');

    $this->jefeDistrital = Usuario::factory()->create(['estado' => 'activo']);
    $this->jefeDistrital->assignRole('jefe_distrital');

    $this->director = Usuario::factory()->create(['estado' => 'activo']);
    $this->director->assignRole('director');

    // Standard user with no special roles
    $this->profesor = Usuario::factory()->create(['estado' => 'activo']);
    $this->profesor->assignRole('profesor');
});

test('superuser can access Panel General and write to it', function () {
    $response = $this->actingAs($this->superuser, 'sanctum')
        ->postJson('/api/v1/admin/ambitos', [
            'nombre' => 'Ambito Test Super',
        ]);

    $response->assertStatus(201);
});

test('restricted roles cannot access Panel General endpoints at all', function () {
    $restrictedUsers = [
        $this->jefeProvincial,
        $this->jefeRegional,
        $this->jefeDistrital,
        $this->director,
    ];

    foreach ($restrictedUsers as $user) {
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/ambitos');

        $response->assertStatus(403)
            ->assertJsonFragment(['error' => 'No tienes permisos para acceder al Panel General.']);

        $responsePost = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/admin/ambitos', [
                'nombre' => 'Ambito Test NonSuper',
            ]);

        $responsePost->assertStatus(403);
    }
});

test('restricted roles can view Años (Panel Curricular) but not modify them', function () {
    $anio = Anio::factory()->create(['nombre' => '1ERO']);

    $restrictedUsers = [
        $this->jefeProvincial,
        $this->jefeRegional,
        $this->jefeDistrital,
        $this->director,
    ];

    foreach ($restrictedUsers as $user) {
        // Can view Años
        $responseGet = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/anios');

        $responseGet->assertStatus(200);

        // Cannot create Años
        $responsePost = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/admin/anios', [
                'nombre' => '2DO',
                'nombre_completo' => 'SEGUNDO AÑO',
                'vigente' => true
            ]);

        $responsePost->assertStatus(403);
    }
});

test('restricted roles can view Planes (Panel Curricular) but not modify them', function () {
    $plan = Plan::factory()->create(['nombre' => 'Plan Curricular Test']);

    $restrictedUsers = [
        $this->jefeProvincial,
        $this->jefeRegional,
        $this->jefeDistrital,
        $this->director,
    ];

    foreach ($restrictedUsers as $user) {
        // Can view Planes
        $responseGet = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/planes');

        $responseGet->assertStatus(200);

        // Cannot create/update/delete Planes
        $responsePost = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/planes', [
                'nombre' => 'Nuevo Plan Prohibido',
                'nombre_completo' => 'Nombre Completo del Nuevo Plan Prohibido',
                'duracion_anios' => 5,
                'plan_ciclo_id' => $plan->plan_ciclo_id,
            ]);

        $responsePost->assertStatus(403);

        $responsePut = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/planes/{$plan->id}", [
                'nombre' => 'Plan Modificado',
                'nombre_completo' => 'Nombre Completo del Plan Modificado',
                'duracion_anios' => 3,
                'plan_ciclo_id' => $plan->plan_ciclo_id,
            ]);

        $responsePut->assertStatus(403);

        $responseDelete = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/planes/{$plan->id}");

        $responseDelete->assertStatus(403);
    }
});
