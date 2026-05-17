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
        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('provincia_id')->after('id')->nullable()->constrained('provincias')->onDelete('restrict');
        });

        // Asignar la provincia de Buenos Aires (id_georef = '06') a las regiones existentes
        $buenosAires = DB::table('provincias')->where('id_georef', '06')->first();

        if ($buenosAires) {
            DB::table('regions')->update(['provincia_id' => $buenosAires->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropForeign(['provincia_id']);
            $table->dropColumn('provincia_id');
        });
    }
};
