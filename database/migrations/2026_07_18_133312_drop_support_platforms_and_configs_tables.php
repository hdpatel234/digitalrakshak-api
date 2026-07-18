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
        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'support_config_id')) {
                // Try to drop the index if it exists, otherwise ignore the exception or use raw sql
                // Wait, since we know it's not there, let's just drop the column
                $table->dropColumn('support_config_id');
            }
        });


        Schema::dropIfExists('support_configs');
        Schema::dropIfExists('support_platforms');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('support_platforms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('platform_name', 100);
            $table->string('platform_code', 50)->unique('platform_code');
            $table->text('description')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });

        Schema::create('support_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('support_platform_id')->index('support_platform_id');
            $table->string('config_name', 100);
            $table->boolean('is_default')->nullable()->default(false)->index('client_support_config');
            $table->string('api_url', 500)->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('api_token')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->longText('additional_config')->nullable();
            $table->enum('default_priority', ['low', 'medium', 'high', 'urgent'])->nullable()->default('medium');
            $table->string('default_department', 100)->nullable();
            $table->string('default_assignee')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->nullable()->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });

        Schema::table('support_configs', function (Blueprint $table) {
            $table->foreign(['support_platform_id'], 'tblsupport_configs_ibfk_2')->references(['id'])->on('support_platforms')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('support_config_id')->nullable();
            $table->unique(['support_config_id', 'external_ticket_id'], 'external_ticket');
        });
    }
};
