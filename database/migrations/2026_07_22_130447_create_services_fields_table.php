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
        Schema::create('services_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('service_id');
            $table->string('field_name', 100);
            $table->string('section', 100)->nullable();
            $table->string('field_label', 100);
            $table->string('field_type', 50);
            $table->boolean('is_required')->nullable()->default(true);
            $table->boolean('is_hidden')->default(false);
            $table->string('or_group_name')->nullable();
            $table->string('validation_regex')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->longText('field_options')->nullable();
            $table->boolean('is_verifiable')->nullable()->default(true);
            $table->string('status', 50)->nullable()->default('active');
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
        Schema::dropIfExists('services_fields');
    }
};
