<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Renombrar otros -> observaciones
        Schema::table('domicilios', function (Blueprint $table) {
            $table->renameColumn('otros', 'observaciones');
        });

        // 2) Clave foránea nullable hacia nacions (país de residencia)
        Schema::table('domicilios', function (Blueprint $table) {
            $table->foreignId('nacion_id')
                ->nullable()
                ->after('persona_id')
                ->constrained('nacions');
        });
    }

    public function down(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nacion_id');
        });

        Schema::table('domicilios', function (Blueprint $table) {
            $table->renameColumn('observaciones', 'otros');
        });
    }
};
