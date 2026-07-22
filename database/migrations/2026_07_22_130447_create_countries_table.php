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
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('iso_code_2', 2)->unique('countries_iso2_unique');
            $table->string('iso_code_3', 3)->unique('countries_iso3_unique');
            $table->string('numeric_code', 3)->nullable();
            $table->string('phone_code', 10);
            $table->string('currency_code', 3)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->string('capital', 100)->nullable();
            $table->string('continent', 50)->nullable()->index('countries_continent');
            $table->string('flag_icon', 50)->nullable();
            $table->string('flag_image', 500)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->longText('timezones')->nullable();
            $table->string('postal_code_format', 100)->nullable();
            $table->string('postal_code_regex')->nullable();
            $table->boolean('is_default')->nullable()->default(false);
            $table->integer('display_order')->nullable()->default(0);
            $table->string('status')->nullable()->default('active')->index('countries_active');
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
        Schema::dropIfExists('countries');
    }
};
