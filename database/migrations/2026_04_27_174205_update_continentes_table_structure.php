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
        Schema::table('continentes', function (Blueprint $table) {
            if (Schema::hasColumn('continentes', 'orden')) {
                $table->dropColumn('orden');
            }
            if (!Schema::hasColumn('continentes', 'vigente')) {
                $table->boolean('vigente')->default(true)->after('nombre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('continentes', function (Blueprint $table) {
            $table->integer('orden')->nullable()->after('nombre');
            $table->dropColumn('vigente');
        });
    }
};
