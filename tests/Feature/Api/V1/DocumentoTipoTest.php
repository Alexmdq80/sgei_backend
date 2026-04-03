<?php

namespace Tests\Feature\Api\V1;

use App\Models\DocumentoTipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentoTipoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_document_types(): void
    {
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
        DocumentoTipo::factory()->count(2)->create(['vigente' => true]);

        $response = $this->getJson('/api/v1/documento-tipos');

        $response->assertStatus(200)
             ->assertJsonCount(8, 'data');    }
}
