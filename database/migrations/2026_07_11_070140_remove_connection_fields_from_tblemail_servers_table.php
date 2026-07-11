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
        Schema::table('email_servers', function (Blueprint $table) {
            $table->dropColumn([
                'host',
                'port',
                'encryption',
                'username',
                'password',
                'timeout',
                'verify_ssl',
                'auth_type',
                'api_key',
                'api_secret',
                'api_endpoint',
                'domain',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_servers', function (Blueprint $table) {
            $table->string('host')->nullable();
            $table->integer('port')->nullable();
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
        });
    }
};
