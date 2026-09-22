<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $ahora = now();
        $ultimaModificacion = Schema::hasTable('calles')
            ? DB::table('calles')->max('updated_at')
            : null;

        DB::table('catalogo_versions')->insertOrIgnore([
            'tabla' => 'calles',
            'version' => (string) ($ultimaModificacion ?? $ahora->format('Y-m-d H:i:s.u')),
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);
    }

    public function down(): void
    {
        DB::table('catalogo_versions')->where('tabla', 'calles')->delete();
    }
};
