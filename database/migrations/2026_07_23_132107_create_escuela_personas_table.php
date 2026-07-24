<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escuela_persona', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedMediumInteger('escuela_id');
            $table->unsignedBigInteger('persona_id');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();

            // Foreign keys
            $table->foreign('escuela_id')->references('id')->on('escuelas')->cascadeOnDelete();
            $table->foreign('persona_id')->references('id')->on('personas')->cascadeOnDelete();

            // Índices
            $table->unique(['escuela_id', 'persona_id', 'role_id']);
            $table->index('persona_id');
            $table->index('escuela_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escuela_persona');
    }
};
