<?php

use App\Models\Escuela;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->user = Usuario::factory()->create();
    $this->actingAs($this->user);
});

test('it logs creation of a model in the correct audit table', function () {
    $escuela = Escuela::factory()->create([
        'nombre' => 'Escuela de Prueba Auditada',
        'cue_anexo' => '123456789'
    ]);

    $this->assertDatabaseHas('audit_entities', [
        'auditable_type' => Escuela::class,
        'auditable_id' => $escuela->id,
        'event' => 'created',
        'user_id' => $this->user->id,
    ]);

    $audit = DB::table('audit_entities')
        ->where('auditable_id', $escuela->id)
        ->first();

    $newValues = json_decode($audit->new_values, true);
    expect($newValues['nombre'])->toBe('Escuela de Prueba Auditada');
    expect($newValues)->not->toHaveKey('created_at'); // Filtered field
});

test('it logs updates of a model with old and new values', function () {
    $escuela = Escuela::factory()->create(['nombre' => 'Nombre Original']);

    $escuela->update(['nombre' => 'Nombre Modificado']);

    $this->assertDatabaseHas('audit_entities', [
        'auditable_id' => $escuela->id,
        'event' => 'updated',
        'user_id' => $this->user->id,
    ]);

    $audit = DB::table('audit_entities')
        ->where('auditable_id', $escuela->id)
        ->where('event', 'updated')
        ->first();

    $oldValues = json_decode($audit->old_values, true);
    $newValues = json_decode($audit->new_values, true);

    expect($oldValues['nombre'])->toBe('Nombre Original');
    expect($newValues['nombre'])->toBe('Nombre Modificado');
});

test('it logs deletions of a model', function () {
    $escuela = Escuela::factory()->create();

    $escuela->delete();

    $this->assertDatabaseHas('audit_entities', [
        'auditable_id' => $escuela->id,
        'event' => 'deleted',
        'user_id' => $this->user->id,
    ]);
});

test('it uses system audit table by default for unconfigured models', function () {
    // Genero is a model that uses AuditableTrait but doesn't have a defined auditGroup
    $genero = \App\Models\Genero::create(['nombre' => 'Otro']);

    $this->assertDatabaseHas('audit_system', [
        'auditable_type' => \App\Models\Genero::class,
        'auditable_id' => $genero->id,
        'event' => 'created',
    ]);
});
