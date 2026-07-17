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
        Schema::create('verification_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_service_data_id');
            $table->enum('verification_status', ['pending', 'in_progress', 'verified', 'failed', 'error'])->default('pending');
            $table->decimal('verification_score', 5, 2)->nullable()->comment('Confidence score 0-100');
            $table->text('verified_value')->nullable()->comment('Verified/corrected value');
            $table->text('original_value')->nullable()->comment('Original value before verification');
            $table->enum('comparison_result', ['match', 'mismatch', 'partial_match', 'not_applicable'])->nullable();
            $table->text('discrepancy_details')->nullable();
            $table->text('verification_notes')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamp('verification_started_at')->nullable();
            $table->timestamp('verification_completed_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable()->comment('User or system that verified');
            $table->enum('verification_method', ['auto', 'manual', 'hybrid'])->default('auto');
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes(); // creates deleted_at

            $table->foreign('candidate_service_data_id')->references('id')->on('candidate_service_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_results');
    }
};
