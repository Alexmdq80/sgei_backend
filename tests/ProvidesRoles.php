<?php

namespace Tests;

use Database\Seeders\RolesAndPermissionsSeeder;

trait ProvidesRoles
{
    /**
     * Seed roles and permissions for tests.
     */
    protected function seedRoles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
