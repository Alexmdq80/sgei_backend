<?php

use App\Models\Escalafon;
use App\Models\Escuela;
use App\Models\PuestoTipo;
use App\Models\Usuario;
use Laravel\Sanctum\Sanctum;
use Tests\ProvidesRoles;

uses(ProvidesRoles::class);

test('admin can create a cupof', function () {
    $this->seedRoles();

    // Crear datos maestros necesarios
    $escalafon = Escalafon::firstOrCreate(['nombre' => 'DOCENTE'], ['vigente' => true]);
    $puestoTipo = PuestoTipo::firstOrCreate(['nombre' => 'CARGO'], ['vigente' => true]);

    $admin = Usuario::factory()->create([
        'email_verified_at' => now(),
    ]);
    $admin->assignRole('superuser');
    Sanctum::actingAs($admin);

    $escuela = Escuela::factory()->create();
    $escalafon = Escalafon::first();
    $puestoTipo = PuestoTipo::first();

    $data = [
        'codigo_cupof' => 'TEST-CUPOF-001',
        'escuela_id' => $escuela->id,
        'escalafon_id' => $escalafon->id,
        'puesto_tipo_id' => $puestoTipo->id,
        'nombre_cargo' => 'Director',
        'cantidad' => 1,
    ];

    $response = $this->postJson('/api/v1/admin/cupofs', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('cupofs', [
        'codigo_cupof' => 'TEST-CUPOF-001',
        'escuela_id' => $escuela->id,
        'created_by' => $admin->id,
    ]);
});
