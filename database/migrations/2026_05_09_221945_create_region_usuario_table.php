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
        Schema::create('region_usuario', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Auditoría
            $table->foreignUuid('created_by')->nullable()->constrained('usuarios');
            $table->foreignUuid('updated_by')->nullable()->constrained('usuarios');
            $table->foreignUuid('deleted_by')->nullable()->constrained('usuarios');

            $table->unique(['usuario_id', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_usuario');
    }
};
