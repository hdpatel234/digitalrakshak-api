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
        Schema::create('verification_workflow_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id')->index('tblverification_workflow_configs_service_id_foreign');
            $table->string('workflow_name');
            $table->json('workflow_steps')->comment('Step-by-step verification workflow');
            $table->boolean('auto_start')->default(true);
            $table->integer('timeout_minutes')->default(60);
            $table->integer('escalation_after_minutes')->default(120);
            $table->unsignedBigInteger('escalation_user_id')->nullable();
            $table->json('notification_config')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_workflow_configs');
    }
};
