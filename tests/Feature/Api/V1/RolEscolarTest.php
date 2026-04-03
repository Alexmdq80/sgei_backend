<?php

namespace Tests\Feature\Api\V1;

use App\Models\RolEscolar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolEscolarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Can list school roles.
     */
    public function test_can_list_school_roles(): void
    {
        // Seed some roles
        RolEscolar::factory()->create(['nombre' => 'Director']);
        RolEscolar::factory()->create(['nombre' => 'Docente']);

        $response = $this->getJson('/api/v1/rol-escolares');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
        
        $response->assertJsonFragment(['nombre' => 'Director']);
        $response->assertJsonFragment(['nombre' => 'Docente']);
    }
}
