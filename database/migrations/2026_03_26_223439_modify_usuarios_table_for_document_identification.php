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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('apellido');
            $table->unsignedTinyInteger('documento_tipo_id')->nullable()->after('nombre');
            $table->string('documento_numero')->nullable()->after('documento_tipo_id');

            $table->foreign('documento_tipo_id')->references('id')->on('documento_tipos');
            $table->index(['documento_tipo_id', 'documento_numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['documento_tipo_id']);
            $table->dropIndex(['documento_tipo_id', 'documento_numero']);
            $table->dropColumn(['documento_tipo_id', 'documento_numero']);
            $table->string('apellido')->after('nombre')->nullable();
        });
    }
};
