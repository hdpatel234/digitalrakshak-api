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
        Schema::create('provider_outages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id')->index('provider_id');
            $table->unsignedBigInteger('service_id')->nullable()->index('service_id');
            $table->enum('outage_type', ['partial', 'full', 'degraded'])->nullable()->default('full');
            $table->timestamp('started_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable()->storedAs('timestampdiff(MINUTE,`started_at`,`ended_at`)');
            $table->longText('affected_services')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index(['started_at', 'ended_at'], 'provider_outages_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_outages');
    }
};
