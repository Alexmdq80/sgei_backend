<?php

use App\Models\DocumentoTipo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list document types', function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    DocumentoTipo::factory()->count(2)->create(['vigente' => true]);

    $response = $this->getJson('/api/v1/documento-tipos');

    $response->assertStatus(200)
         ->assertJsonCount(8, 'data');
});
