<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agregar columnas de ID
        Schema::table('cupofs', function (Blueprint $table) {
            $table->unsignedTinyInteger('escalafon_id')->nullable()->after('asignatura_id');
            $table->unsignedTinyInteger('puesto_tipo_id')->nullable()->after('escalafon_id');
        });

        // 2. Migrar datos existentes
        $escalafones = DB::table('escalafones')->pluck('id', 'nombre')->toArray();
        $puestoTipos = DB::table('puesto_tipos')->pluck('id', 'nombre')->toArray();

        // Mapeo simple (enum a string uppercase)
        $cupofs = DB::table('cupofs')->get(['id', 'escalafon', 'tipo_puesto']);
        foreach ($cupofs as $cupof) {
            $eId = $escalafones[strtoupper($cupof->escalafon)] ?? null;
            $pId = null;
            
            // Mapeo manual para puesto tipos con tildes
            $tipo = strtoupper($cupof->tipo_puesto);
            if ($tipo === 'CARGO') $pId = $puestoTipos['CARGO'] ?? null;
            elseif ($tipo === 'HORAS_CATEDRA') $pId = $puestoTipos['HORAS CÁTEDRA'] ?? null;
            elseif ($tipo === 'MODULOS') $pId = $puestoTipos['MÓDULOS'] ?? null;

            DB::table('cupofs')->where('id', $cupof->id)->update([
                'escalafon_id' => $eId,
                'puesto_tipo_id' => $pId
            ]);
        }

        // 3. Eliminar columnas antiguas y establecer FKs
        Schema::table('cupofs', function (Blueprint $table) {
            $table->dropColumn(['escalafon', 'tipo_puesto']);
            
            $table->foreign('escalafon_id')->references('id')->on('escalafones')->onDelete('restrict');
            $table->foreign('puesto_tipo_id')->references('id')->on('puesto_tipos')->onDelete('restrict');
            
            $table->unsignedTinyInteger('escalafon_id')->nullable(false)->change();
            $table->unsignedTinyInteger('puesto_tipo_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cupofs', function (Blueprint $table) {
            $table->dropForeign(['puesto_tipo_id']);
            $table->dropForeign(['escalafon_id']);
            
            $table->enum('escalafon', ['docente', 'auxiliar', 'administrativo'])->default('docente')->after('asignatura_id');
            $table->enum('tipo_puesto', ['cargo', 'horas_catedra', 'modulos'])->default('cargo')->after('escalafon');
        });

        // Migrar de vuelta (aproximado)
        DB::table('cupofs')->update([
            'escalafon' => 'docente',
            'tipo_puesto' => 'cargo'
        ]);

        Schema::table('cupofs', function (Blueprint $table) {
            $table->dropColumn(['escalafon_id', 'puesto_tipo_id']);
        });
    }
};
