<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogos maestros versionados: nombre real de la tabla.
     *
     * @var array<int, string>
     */
    private const CATALOGOS = [
        'nacions',
        'provincias',
        'regions',
        'departamentos',
        'localidads',
        'documento_tipos',
        'documento_situacions',
        'sexos',
        'generos',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catalogo_versions', function (Blueprint $table): void {
            $table->string('tabla', 64)->primary();
            $table->string('version', 64);
            $table->timestamps();
        });

        $this->inicializarVersions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_versions');
    }

    /**
     * Puebla la tabla con la última modificación de cada catálogo ya existente.
     */
    private function inicializarVersions(): void
    {
        $ahora = now();

        foreach (self::CATALOGOS as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            $ultimaModificacion = DB::table($tabla)->max('updated_at');

            DB::table('catalogo_versions')->insertOrIgnore([
                'tabla' => $tabla,
                'version' => (string) ($ultimaModificacion ?? $ahora->format('Y-m-d H:i:s.u')),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
};
