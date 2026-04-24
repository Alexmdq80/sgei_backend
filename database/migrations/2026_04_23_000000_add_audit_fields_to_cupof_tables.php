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
        Schema::table('cupofs', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('updated_at');
            $table->uuid('updated_by')->nullable()->after('created_by');
            
            $table->foreign('created_by')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('usuarios')->onDelete('set null');
        });

        Schema::table('cupof_movimientos', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('updated_at');
            $table->uuid('updated_by')->nullable()->after('created_by');
            
            $table->foreign('created_by')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cupof_movimientos', function (Blueprint $table) {
            if (Schema::hasColumn('cupof_movimientos', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('cupof_movimientos', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('cupofs', function (Blueprint $table) {
            if (Schema::hasColumn('cupofs', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('cupofs', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
