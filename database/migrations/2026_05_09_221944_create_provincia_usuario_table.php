<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function run(): void
    {
        Schema::create('provincia_usuario', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('provincia_id')->constrained('provincias')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('usuarios');
            $table->foreignId('updated_by')->nullable()->constrained('usuarios');
            $table->foreignId('deleted_by')->nullable()->constrained('usuarios');

            $table->unique(['usuario_id', 'provincia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provincia_usuario');
    }
};
