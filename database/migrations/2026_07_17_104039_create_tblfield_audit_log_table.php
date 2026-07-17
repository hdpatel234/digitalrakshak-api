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
        Schema::create('field_audit_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_service_data_id');
            $table->unsignedBigInteger('field_id');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->enum('action', ['create', 'update', 'verify', 'delete']);
            $table->string('change_reason', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('candidate_service_data_id')->references('id')->on('candidate_service_data');
            $table->foreign('field_id')->references('id')->on('services_fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_audit_log');
    }
};
