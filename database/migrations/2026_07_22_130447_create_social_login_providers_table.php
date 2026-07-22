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
        Schema::create('social_login_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider_name', 100);
            $table->string('provider_code', 50)->unique('provider_code');
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('client_id', 500);
            $table->text('client_secret');
            $table->string('redirect_url', 500)->nullable();
            $table->longText('scopes')->nullable();
            $table->longText('auth_parameters')->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_icon', 50)->nullable();
            $table->string('button_color', 20)->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->boolean('is_enabled')->nullable()->default(false);
            $table->boolean('is_default')->nullable()->default(false);
            $table->integer('total_users')->nullable()->default(0);
            $table->integer('total_connections')->nullable()->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index(['is_enabled', 'display_order'], 'social_providers_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_login_providers');
    }
};
