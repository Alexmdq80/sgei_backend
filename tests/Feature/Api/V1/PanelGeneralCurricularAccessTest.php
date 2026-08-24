<?php

namespace Tests\Feature\Api\V1;

use App\Models\Plan;
use App\Models\Usuario;
use App\Models\Ambito;
use App\Models\AnioPlan;
use Tests\ProvidesRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, ProvidesRoles::class);

beforeEach(function () {
    $this->seedRoles();

    // Super Administrador (acceso total al Panel General)
    $this->superuser = Usuario::factory()->create(['estado' => 'activo']);
    $this->superuser->assignRole('superuser');

    // Director institucional (rol restringido SOLO a lectura sobre planes/asignaturas)
    $this->director = Usuario::factory()->create(['estado' => 'activo']);
    $this->director->assignRole('director');

        // Profesor institucional (sin permisos curriculares)
    $this->profesor = Usuario::factory()->create(['estado' => 'activo']);
    $this->profesor->assignRole('profesor');

    // Año de plan válido para tests de asignaturas
    $this->anioPlan = AnioPlan::factory()->create();
});

test('superuser puede acceder al Panel General y escribir', function () {
    $response = $this->actingAs($this->superuser, 'sanctum')
        ->postJson('/api/v1/admin/ambitos', [
            'nombre' => 'Ambito Test Super',
        ]);

    $response->assertStatus(201);
});

test('roles restringidos no pueden acceder a los endpoints del Panel General', function () {
    foreach ([$this->director, $this->profesor] as $user) {
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/ambitos');

                // El middleware block_panel_general rechaza con 403 antes de llegar al controlador/validación.
        $response->assertStatus(403);
    }
});

test('director puede listar planes (solo lectura) pero no crearlos', function () {
    Plan::factory()->count(3)->create();

    // GET → read-only permitido
    $responseGet = $this->actingAs($this->director, 'sanctum')
        ->getJson('/api/v1/planes');

    $responseGet->assertStatus(200);

    // POST → prohibido (rol read-only)
    $responsePost = $this->actingAs($this->director, 'sanctum')
        ->postJson('/api/v1/planes', [
            'nombre' => 'Plan Prohibido',
            'nombre_completo' => 'Nombre Completo del Plan Prohibido',
            'duracion_anios' => 5,
            'plan_ciclo_id' => Plan::factory()->create()->plan_ciclo_id,
        ]);

    $responsePost->assertStatus(403);
});

test('profesor no puede gestionar planes ni asignaturas', function () {
    $plan = Plan::factory()->create();

    $this->actingAs($this->profesor, 'sanctum')
        ->getJson('/api/v1/planes')
        ->assertStatus(403);

    $this->actingAs($this->profesor, 'sanctum')
        ->postJson('/api/v1/planes', [
            'nombre' => 'Nuevo Plan',
            'nombre_completo' => 'Completo',
            'duracion_anios' => 3,
            'plan_ciclo_id' => $plan->plan_ciclo_id,
        ])->assertStatus(403);

        $this->actingAs($this->profesor, 'sanctum')
        ->postJson('/api/v1/asignaturas', [
            'nombre' => 'Física',
            'anio_plan_id' => $this->anioPlan->id,
            'horas_semanales' => 4,
        ])->assertStatus(403);
});

