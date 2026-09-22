<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->foreignId('provincia_id')->nullable()->after('nacion_id')->constrained('provincias');
        });
        Schema::table('domicilios', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->after('provincia_id')->constrained('departamentos');
        });
    }

    public function down(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('departamento_id');
        });
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('provincia_id');
        });
    }
};
