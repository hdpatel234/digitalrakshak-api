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
        Schema::table('provider_api_configs', function (Blueprint $table) {
            $table->text('public_key')->nullable()->after('api_token');
            $table->text('private_key')->nullable()->after('public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_api_configs', function (Blueprint $table) {
            $table->dropColumn(['public_key', 'private_key']);
        });
    }
};
