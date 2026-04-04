<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('check id returns session id', function () {
    $response = $this->getJson('/api/v1/check-id');

    $response->assertStatus(200);
});
