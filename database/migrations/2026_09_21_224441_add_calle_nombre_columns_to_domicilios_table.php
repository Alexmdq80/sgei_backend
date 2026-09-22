<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->string('calle_nombre', 255)->nullable()->after('calle_id');
            $table->string('calle_entre_1_nombre', 255)->nullable()->after('calle_entre_1_id');
            $table->string('calle_entre_2_nombre', 255)->nullable()->after('calle_entre_2_id');
        });
    }

    public function down(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropColumn(['calle_nombre', 'calle_entre_1_nombre', 'calle_entre_2_nombre']);
        });
    }
};
