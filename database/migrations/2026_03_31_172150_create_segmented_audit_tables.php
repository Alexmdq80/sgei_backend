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
        $tables = ['audit_entities', 'audit_academic', 'audit_system'];

        foreach ($tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('auditable_type');
                $table->uuid('auditable_id');
                $table->string('event'); // created, updated, deleted, restored
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('url')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->uuid('user_id')->nullable(); // Who did it
                $table->json('tags')->nullable();
                $table->timestamps();

                $table->index(['auditable_type', 'auditable_id']);
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_system');
        Schema::dropIfExists('audit_academic');
        Schema::dropIfExists('audit_entities');
    }
};
