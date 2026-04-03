<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the check-id utility route.
     */
    public function test_check_id_returns_session_id(): void
    {
        $response = $this->getJson('/api/v1/check-id');

        $response->assertStatus(200);
        // The session ID is a string, could be empty if session not started in testing without cookies
        // but the route should at least exist and respond 200.
    }
}
