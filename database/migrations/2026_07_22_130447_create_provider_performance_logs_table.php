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
        Schema::create('provider_performance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('assignment_id')->nullable()->index('assignment_id');
            $table->integer('response_time_ms')->nullable();
            $table->integer('status_code')->nullable();
            $table->boolean('success')->nullable()->default(true);
            $table->text('error_message')->nullable();
            $table->timestamp('logged_at')->nullable()->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index(['provider_id', 'logged_at'], 'provider_performance_provider');
            $table->index(['service_id', 'logged_at'], 'provider_performance_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_performance_logs');
    }
};
