<?php

use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('cualquier usuario puede listar los roles institucionales', function () {
    $response = $this->getJson('/api/v1/rol-escolares');

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'guard_name']
        ]);
        
    // Verificar que al menos existan los roles básicos sembrados
    $response->assertJsonFragment(['name' => 'director'])
             ->assertJsonFragment(['name' => 'profesor'])
             ->assertJsonFragment(['name' => 'estudiante']);
});
