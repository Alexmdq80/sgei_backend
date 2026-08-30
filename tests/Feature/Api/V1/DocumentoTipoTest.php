<?php

use App\Models\DocumentoTipo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list document types', function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    DocumentoTipo::factory()->count(2)->create(['vigente' => true]);

    $user = \App\Models\Usuario::factory()->create();
    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/documento-tipos');

    $response->assertStatus(200)
        ->assertJsonCount(DocumentoTipo::count());

});
