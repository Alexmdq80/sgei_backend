<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn(['posee_cpi_si', 'posee_docExt_si']);
        });

        Schema::table('personas', function (Blueprint $table) {
            $table->string('foto_path')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });

        Schema::table('personas', function (Blueprint $table) {
            $table->boolean('posee_cpi_si')->default(false);
            $table->boolean('posee_docExt_si')->default(false);
        });
    }
};
