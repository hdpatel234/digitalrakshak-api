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
        Schema::create('user_datetime_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique('unique_user_datetime');
            $table->string('timezone', 100)->nullable()->default('UTC');
            $table->string('date_format', 50)->nullable()->default('YYYY-MM-DD');
            $table->enum('time_format', ['12', '24'])->nullable()->default('24');
            $table->enum('first_day_of_week', ['monday', 'sunday', 'saturday'])->nullable()->default('monday');
            $table->enum('week_starts_on', ['sunday', 'monday'])->nullable()->default('monday');
            $table->boolean('show_week_numbers')->nullable()->default(false);
            $table->enum('calendar_view', ['month', 'week', 'day', 'agenda'])->nullable()->default('month');
            $table->time('working_hours_start')->nullable()->default('09:00:00');
            $table->time('working_hours_end')->nullable()->default('17:00:00');
            $table->longText('working_days')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_datetime_preferences');
    }
};
