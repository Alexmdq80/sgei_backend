<?php

namespace Tests\Feature\Api\V1;

use App\Models\Escuela;
use App\Models\Nivel;
use App\Models\RolEscolar;
use App\Models\Sector;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EscuelaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_schools(): void
    {
        Escuela::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/escuelas');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_search_schools_by_name(): void
    {
        Escuela::factory()->create(['nombre' => 'Escuela Normal']);
        Escuela::factory()->create(['nombre' => 'Colegio Nacional']);

        $response = $this->getJson('/api/v1/escuelas?search=Normal');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['nombre' => 'Escuela Normal']);
    }

    public function test_can_get_niveles(): void
    {
        Nivel::factory()->create(['nombre' => 'Primario', 'vigente' => true]);
        Nivel::factory()->create(['nombre' => 'Secundario', 'vigente' => true]);
        Nivel::factory()->create(['nombre' => 'Obsoleto', 'vigente' => false]);

        $response = $this->getJson('/api/v1/niveles');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['nombre' => 'Primario'])
            ->assertJsonMissing(['nombre' => 'Obsoleto']);
    }

    public function test_can_get_sectores(): void
    {
        Sector::factory()->create(['nombre' => 'Estatal', 'vigente' => true]);
        Sector::factory()->create(['nombre' => 'Privado', 'vigente' => true]);

        $response = $this->getJson('/api/v1/sectores');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['nombre' => 'Estatal']);
    }

    public function test_authenticated_user_can_request_to_join_a_school(): void
    {
        $user = Usuario::factory()->create();
        $escuela = Escuela::factory()->create();
        $rol = RolEscolar::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/auth/escuelas/join', [
                'escuela_id' => $escuela->id,
                'rol_escolar_id' => $rol->id
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Solicitud enviada con éxito. Espere la aprobación del administrador.']);

        $this->assertDatabaseHas('escuela_usuario', [
            'usuario_id' => $user->id,
            'escuela_id' => $escuela->id,
            'rol_escolar_id' => $rol->id,
            'verified_at' => null
        ]);
    }

    public function test_authenticated_user_can_cancel_join_request(): void
    {
        $user = Usuario::factory()->create();
        $escuela = Escuela::factory()->create();
        $rol = RolEscolar::factory()->create();

        // Crear la solicitud previa usando el modelo EscuelaUsuario
        \App\Models\EscuelaUsuario::create([
            'usuario_id' => $user->id,
            'escuela_id' => $escuela->id,
            'rol_escolar_id' => $rol->id,
            'verified_at' => null
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/auth/escuelas/cancel-join', [
                'escuela_id' => $escuela->id
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Solicitud cancelada.']);

        $this->assertSoftDeleted('escuela_usuario', [
            'usuario_id' => $user->id,
            'escuela_id' => $escuela->id
        ]);
    }
}
