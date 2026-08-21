<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('distrito_usuario');
        Schema::dropIfExists('region_usuario');
        Schema::dropIfExists('provincia_usuario');
    }

    public function down(): void
    {
        // No recreamos las tablas (se pierden datos de jefaturas)
    }
};
