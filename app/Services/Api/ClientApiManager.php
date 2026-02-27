<?php

namespace App\Services\Api;

use App\Models\Client;
use App\Models\ClientApiKey;
use App\Models\ClientApiLog;
use App\Models\ClientApiQuota;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class ClientApiManager
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Generate new API key for client
     */
    public function generateApiKey(string $keyName, string $type = 'production', array $permissions = []): ClientApiKey
    {
        // Generate unique API key and secret
        $apiKey = 'dr_' . $type . '_' . Str::random(32);
        $apiSecret = Str::random(64);

        return ClientApiKey::create([
            'client_id' => $this->client->id,
            'key_name' => $keyName,
            'api_key' => $apiKey,
            'api_secret' => Hash::make($apiSecret), // Store hashed secret
            'key_type' => $type,
            'permissions' => $permissions,
            'rate_limit' => $this->client->api_rate_limit_default,
            'rate_limit_per_day' => $this->client->api_daily_limit_default,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Validate API key and secret
     */
    public function validateApiKey(string $apiKey, ?string $apiSecret = null): ?ClientApiKey
    {
        $key = ClientApiKey::where('api_key', $apiKey)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$key) {
            return null;
        }

        // If secret is required and provided, validate it
        if ($apiSecret && !Hash::check($apiSecret, $key->api_secret)) {
            return null;
        }

        // Check rate limits
        if (!$this->checkRateLimits($key)) {
            return null;
        }

        // Update last used
        $key->update([
            'last_used_at' => now(),
            'last_used_ip' => request()->ip(),
            'total_requests' => $key->total_requests + 1
        ]);

        // Update quota usage
        $this->incrementQuota($key);

        return $key;
    }

    /**
     * Check rate limits
     */
    protected function checkRateLimits(ClientApiKey $key): bool
    {
        $cacheKey = "api_rate_limit_{$key->id}_" . now()->format('Y-m-d-H-i');
        
        // Per minute rate limit
        $minuteKey = $cacheKey . '_minute';
        $minuteCount = Cache::increment($minuteKey);
        if ($minuteCount == 1) {
            Cache::expire($minuteKey, 60);
        }
        
        if ($minuteCount > $key->rate_limit) {
            $this->logRequest($key, null, 'rate_limited', 'Rate limit exceeded per minute');
            return false;
        }

        // Per day rate limit
        $dayKey = "api_rate_limit_{$key->id}_" . now()->format('Y-m-d');
        $dayCount = Cache::increment($dayKey);
        if ($dayCount == 1) {
            Cache::expire($dayKey, 86400);
        }

        if ($dayCount > $key->rate_limit_per_day) {
            $this->logRequest($key, null, 'rate_limited', 'Daily rate limit exceeded');
            return false;
        }

        return true;
    }

    /**
     * Check if key has permission for endpoint
     */
    public function hasPermission(ClientApiKey $key, string $endpoint, string $method): bool
    {
        $permissions = $key->permissions ?? [];
        
        // If no permissions defined, allow all
        if (empty($permissions)) {
            return true;
        }

        // Check specific permission
        foreach ($permissions as $permission) {
            if ($this->matchPermission($permission, $endpoint, $method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match permission pattern
     */
    protected function matchPermission(string $permission, string $endpoint, string $method): bool
    {
        // Format: "method:endpoint" or "endpoint"
        $parts = explode(':', $permission);
        
        if (count($parts) == 2) {
            return strtoupper($parts[0]) == $method && $this->matchEndpoint($parts[1], $endpoint);
        }
        
        return $this->matchEndpoint($permission, $endpoint);
    }

    /**
     * Match endpoint with wildcards
     */
    protected function matchEndpoint(string $pattern, string $endpoint): bool
    {
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match('#^' . $pattern . '$#', $endpoint);
    }

    /**
     * Log API request
     */
    public function logRequest(
        ?ClientApiKey $key,
        $responseCode,
        string $status,
        ?string $error = null,
        array $metadata = []
    ): void {
        ClientApiLog::create([
            'client_id' => $this->client->id,
            'api_key_id' => $key?->id,
            'endpoint' => request()->path(),
            'method' => request()->method(),
            'request_headers' => request()->headers->all(),
            'request_body' => request()->all(),
            'response_code' => $responseCode,
            'status' => $status,
            'error_message' => $error,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'response_time_ms' => $metadata['response_time'] ?? null
        ]);
    }

    /**
     * Increment quota usage
     */
    protected function incrementQuota(ClientApiKey $key): void
    {
        $today = now()->startOfDay();
        $quota = ClientApiQuota::firstOrCreate(
            [
                'client_id' => $this->client->id,
                'period_start' => $today,
                'period_end' => $today->copy()->endOfDay()
            ],
            [
                'requests_limit' => $key->rate_limit_per_day,
                'requests_used' => 0,
                'reset_at' => $today->copy()->addDay()
            ]
        );

        $quota->increment('requests_used');
    }

    /**
     * Revoke API key
     */
    public function revokeApiKey(ClientApiKey $key, string $reason = null): void
    {
        $key->update([
            'status' => 'revoked',
            'deleted_at' => now()
        ]);

        // Log revocation
        activity()
            ->performedOn($key)
            ->withProperties(['reason' => $reason])
            ->log('API key revoked');
    }
}