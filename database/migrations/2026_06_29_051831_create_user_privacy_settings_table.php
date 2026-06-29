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
        Schema::create('user_privacy_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique('unique_user_privacy');
            $table->enum('profile_visibility', ['public', 'private', 'contacts_only'])->nullable()->default('contacts_only');
            $table->boolean('show_email')->nullable()->default(false);
            $table->boolean('show_phone')->nullable()->default(false);
            $table->boolean('show_activity')->nullable()->default(true);
            $table->boolean('allow_data_collection')->nullable()->default(true);
            $table->boolean('allow_marketing_emails')->nullable()->default(false);
            $table->boolean('allow_analytics')->nullable()->default(true);
            $table->longText('cookie_consent')->nullable();
            $table->integer('data_retention_preference')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_privacy_settings');
    }
};
