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
        Schema::create('social_login_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id')->nullable()->index('provider_id');
            $table->string('email')->nullable()->index('social_login_attempts_email');
            $table->string('ip_address', 45)->nullable()->index('social_login_attempts_ip');
            $table->text('user_agent')->nullable();
            $table->enum('status', ['success', 'failed', 'cancelled', 'error'])->nullable()->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('social_login_attempts_created');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_login_attempts');
    }
};
