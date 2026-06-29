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
        Schema::create('email_servers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('server_name');
            $table->unsignedBigInteger('server_type_id')->index('email_servers_type');
            $table->boolean('is_default')->nullable()->default(false);
            $table->integer('priority')->nullable()->default(0)->index('email_servers_priority');
            $table->string('host');
            $table->integer('port');
            $table->enum('encryption', ['none', 'ssl', 'tls', 'starttls'])->nullable()->default('tls');
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->integer('timeout')->nullable()->default(30);
            $table->boolean('verify_ssl')->nullable()->default(true);
            $table->enum('auth_type', ['plain', 'login', 'cram-md5', 'oauth2', 'api_key'])->nullable()->default('plain');
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('api_endpoint', 500)->nullable();
            $table->string('domain')->nullable();
            $table->integer('rate_limit_per_minute')->nullable();
            $table->integer('rate_limit_per_hour')->nullable();
            $table->integer('rate_limit_per_day')->nullable();
            $table->string('default_from_email')->nullable();
            $table->string('default_from_name')->nullable();
            $table->string('default_reply_to')->nullable();
            $table->string('server_group', 100)->nullable()->default('default');
            $table->integer('weight')->nullable()->default(1);
            $table->enum('status', ['active', 'inactive', 'maintenance', 'failing'])->nullable()->default('active');
            $table->timestamp('health_check_at')->nullable();
            $table->string('health_check_status', 50)->nullable();
            $table->text('last_error')->nullable();
            $table->bigInteger('success_count')->nullable()->default(0);
            $table->bigInteger('failure_count')->nullable()->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['server_group', 'status'], 'email_servers_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_servers');
    }
};
