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
        Schema::create('authentication_audits', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type')->nullable();
            $table->uuid('auditable_id')->nullable();
            $table->string('event');
            $table->string('attempted_email')->nullable();
            $table->string('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('tags')->nullable();
            $table->json('details')->nullable();
            $table->string('audit_driver')->default('authentication');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('usuario_id')->constrained('usuarios');
            $table->string('token', 100)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->string('device_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
        Schema::dropIfExists('authentication_audits');
    }
};
