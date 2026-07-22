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
        Schema::create('candidate_service_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_item_id');
            $table->integer('field_id');
            $table->text('field_value')->nullable();
            $table->boolean('is_verified')->nullable()->default(false);
            $table->timestamp('verified_at')->nullable()->index('idx_candidate_service_data_verification');
            $table->integer('verified_by')->nullable();
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
        Schema::dropIfExists('candidate_service_data');
    }
};
