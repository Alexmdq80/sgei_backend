<?php

use App\Models\Plan;
use App\Models\PlanCiclo;
use App\Models\Usuario;
use Tests\ProvidesRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, ProvidesRoles::class);

beforeEach(function () {
    $this->seedRoles();
    
    // Crear un Supervisor Curricular
    $this->supervisor = Usuario::factory()->create(['estado' => 'activo']);
    $this->supervisor->assignRole('supervisor_curricular');

    // Crear un Usuario común (ej. Profesor)
    $this->profesor = Usuario::factory()->create(['estado' => 'activo']);
    $this->profesor->assignRole('profesor');
});

test('un supervisor puede listar los planes', function () {
    Plan::factory()->count(3)->create();

    $response = $this->actingAs($this->supervisor, 'sanctum')
        ->getJson('/api/v1/planes');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('un supervisor puede crear un plan', function () {
    $ciclo = PlanCiclo::factory()->create();
    
    $data = [
        'nombre' => 'Nuevo Plan Test',
        'nombre_completo' => 'Nombre Completo del Nuevo Plan Test',
        'duracion_anios' => 5,
        'resolucion' => 'Res. 101/26',
        'orientacion' => 'Informática',
        'plan_ciclo_id' => $ciclo->id,
    ];

    $response = $this->actingAs($this->supervisor, 'sanctum')
        ->postJson('/api/v1/planes', $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['nombre' => 'Nuevo Plan Test']);
    
    $this->assertDatabaseHas('plans', ['nombre' => 'Nuevo Plan Test']);
});

test('un supervisor puede actualizar un plan', function () {
    $plan = Plan::factory()->create(['nombre' => 'Plan Original']);
    
    $data = [
        'nombre' => 'Plan Actualizado',
        'nombre_completo' => 'Nombre Completo Actualizado',
        'duracion_anios' => 3,
        'plan_ciclo_id' => $plan->plan_ciclo_id,
    ];

    $response = $this->actingAs($this->supervisor, 'sanctum')
        ->putJson("/api/v1/planes/{$plan->id}", $data);

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'Plan Actualizado']);
});

test('un supervisor puede eliminar un plan', function () {
    $plan = Plan::factory()->create();

    $response = $this->actingAs($this->supervisor, 'sanctum')
        ->deleteJson("/api/v1/planes/{$plan->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('plans', ['id' => $plan->id]);
});

test('un usuario sin permisos no puede gestionar planes', function () {
    $response = $this->actingAs($this->profesor, 'sanctum')
        ->getJson('/api/v1/planes');

    $response->assertStatus(403);
});

test('la creacion de plan requiere campos obligatorios', function () {
    $response = $this->actingAs($this->supervisor, 'sanctum')
        ->postJson('/api/v1/planes', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['nombre', 'nombre_completo', 'duracion_anios', 'plan_ciclo_id']);
});
