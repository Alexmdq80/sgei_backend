<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('nacimiento_fecha');
        });

        Schema::table('contactos', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });

        Schema::table('contactos', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
