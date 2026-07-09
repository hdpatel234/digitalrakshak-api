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
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('provider_type');
            $table->dropColumn(['support_email', 'support_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn('logo');
            $table->string('support_email')->nullable();
            $table->string('support_phone', 50)->nullable();
        });
    }
};
