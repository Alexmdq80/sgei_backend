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
        Schema::table('generos', function (Blueprint $table) {
            if (Schema::hasColumn('generos', 'orden')) {
                $table->dropColumn('orden');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generos', function (Blueprint $table) {
            $table->integer('orden')->nullable()->after('nombre');
        });
    }
};
