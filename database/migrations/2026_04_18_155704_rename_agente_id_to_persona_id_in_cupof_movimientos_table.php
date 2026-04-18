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
        Schema::table('cupof_movimientos', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['agente_id']);
            
            // Rename the column
            $table->renameColumn('agente_id', 'persona_id');
            
            // Add new foreign key pointing to personas
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cupof_movimientos', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->renameColumn('persona_id', 'agente_id');
            $table->foreign('agente_id')->references('id')->on('agentes')->onDelete('cascade');
        });
    }
};
