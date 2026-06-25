<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if Protean already exists to avoid duplicates
        $existing = DB::table('service_providers')->where('provider_code', 'protean')->first();
        if ($existing) {
            return;
        }

        // 1. Insert Protean into tblservice_providers
        $providerId = DB::table('service_providers')->insertGetId([
            'provider_name' => 'Protean',
            'provider_code' => 'protean',
            'provider_type' => 'api',
            'description' => 'Protean API integration for identity, banking, employment and other verifications.',
            'website' => 'https://risewithprotean.io',
            'status' => 'active',
            'is_default' => 0,
            'priority' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert Sandbox Config
        DB::table('provider_api_configs')->insert([
            'provider_id' => $providerId,
            'config_name' => 'Sandbox',
            'environment' => 'sandbox',
            'is_active' => 1,
            'is_default' => 1,
            'base_url' => 'https://uat.risewithprotean.io',
            'api_version' => '1.0.0',
            'timeout_seconds' => 60,
            'max_retries' => 3,
            'retry_delay_seconds' => 5,
            'auth_type' => 'oauth2',
            'api_key' => 'VdMM80JNMwUG7A4Jn0n3dodE1Pk1pAXnPvP75zSYHZHaV8p6', // Default SandBox API Key
            'api_secret' => '6m7XMl3E9Fy6d8zWyb3CK564uei6C8eaUNOvyinEvIGre9advO0zsEEr9unnDT9a', // Default SandBox Secret Key
            'ssl_cert_path' => 'protean/protean-public-key-2048.pem',
            'ssl_key_path' => 'protean/server-key-2048.pem',
            'rate_limit_per_minute' => 60,
            'verify_ssl' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 3. Insert Production Config Placeholder
        DB::table('provider_api_configs')->insert([
            'provider_id' => $providerId,
            'config_name' => 'Production',
            'environment' => 'production',
            'is_active' => 0,
            'is_default' => 0,
            'base_url' => 'https://risewithprotean.io',
            'api_version' => '1.0.0',
            'timeout_seconds' => 60,
            'max_retries' => 3,
            'retry_delay_seconds' => 5,
            'auth_type' => 'oauth2',
            'api_key' => '',
            'api_secret' => '',
            'ssl_cert_path' => 'protean/protean-public-key-production.pem',
            'ssl_key_path' => 'protean/server-key-production.pem',
            'rate_limit_per_minute' => 60,
            'verify_ssl' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $provider = DB::table('service_providers')->where('provider_code', 'protean')->first();
        if ($provider) {
            DB::table('provider_api_configs')->where('provider_id', $provider->id)->delete();
            DB::table('service_providers')->where('id', $provider->id)->delete();
        }
    }
};
