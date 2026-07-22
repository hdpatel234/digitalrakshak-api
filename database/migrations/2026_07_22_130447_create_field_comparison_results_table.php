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
        Schema::create('field_comparison_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('verification_result_id');
            $table->text('user_value')->nullable();
            $table->text('api_value')->nullable();
            $table->enum('comparison_result', ['exact_match', 'partial_match', 'info_not_available', 'verification_failed']);
            $table->decimal('confidence_score', 5)->nullable();
            $table->text('discrepancy_notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_comparison_results');
    }
};
