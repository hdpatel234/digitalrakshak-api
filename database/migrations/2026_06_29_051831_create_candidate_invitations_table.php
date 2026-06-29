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
        Schema::create('candidate_invitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('candidate_id');
            $table->integer('client_id');
            $table->unsignedBigInteger('package_id')->nullable()->index('package_id');
            $table->string('invitation_type')->nullable()->default('package');
            $table->string('invitation_token')->unique('invitation_token');
            $table->string('form_link', 500);
            $table->longText('form_data')->nullable();
            $table->integer('invited_by')->nullable();
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index('idx_invitations_expires');
            $table->integer('reminder_count')->nullable()->default(0);
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 50)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['invitation_token', 'status'], 'idx_invitations_token_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_invitations');
    }
};
