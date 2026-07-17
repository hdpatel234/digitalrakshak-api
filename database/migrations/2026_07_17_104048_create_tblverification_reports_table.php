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
        Schema::create('verification_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_service_id');
            $table->string('report_type', 50);
            $table->json('report_data');
            $table->text('summary')->nullable();
            $table->enum('overall_status', ['verified', 'partial', 'failed', 'pending'])->default('pending');
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->string('report_path', 255)->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->integer('downloaded_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('candidate_service_id')->references('id')->on('candidate_services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_reports');
    }
};
