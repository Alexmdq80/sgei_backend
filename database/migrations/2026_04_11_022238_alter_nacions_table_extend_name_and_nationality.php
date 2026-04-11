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
        Schema::table('nacions', function (Blueprint $col) {
            $col->string('nombre', 100)->change();
            $col->string('nacionalidad', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nacions', function (Blueprint $col) {
            $col->string('nombre', 20)->change();
            $col->string('nacionalidad', 20)->nullable()->change();
        });
    }
};
