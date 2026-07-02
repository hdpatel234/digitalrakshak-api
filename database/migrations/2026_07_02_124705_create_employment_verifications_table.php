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
        Schema::create('employment_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_service_id');
            $table->string('token')->unique();
            $table->string('company_email');
            $table->json('candidate_data')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'needs_changes'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->foreign('candidate_service_id')->references('id')->on('candidate_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_verifications');
    }
};
