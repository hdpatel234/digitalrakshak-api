<?php

namespace App\Services\Api;

use App\Models\Client;
use App\Models\ClientApiKey;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Enums\EnvironmentEnum;
use App\Enums\StatusEnum;
use App\Services\ClientApiKeyService;
use App\Services\ClientApiLogService;
use App\Services\ClientApiQuotaService;

class ClientApiManager
{
    protected $client;
    protected $clientApiKeyService;
    protected $clientApiLogService;
    protected $clientApiQuotaService;

    public function __construct(
        Client $client,
        ClientApiKeyService $clientApiKeyService,
        ClientApiLogService $clientApiLogService,
        ClientApiQuotaService $clientApiQuotaService
    ) {
        $this->client = $client;
        $this->clientApiKeyService = $clientApiKeyService;
        $this->clientApiLogService = $clientApiLogService;
        $this->clientApiQuotaService = $clientApiQuotaService;
    }

    /**
     * Generate new API key for client
     */
    public function generateApiKey(string $keyName, string $type = EnvironmentEnum::PRODUCTION->value, array $permissions = []): ClientApiKey
    {
        // Generate unique API key and secret
        $apiKey = 'dr_' . $type . '_' . Str::random(32);
        $apiSecret = Str::random(64);

        return $this->clientApiKeyService->create([
            $this->clientApiKeyService->clientId() => $this->client->id,
            $this->clientApiKeyService->keyName() => $keyName,
            $this->clientApiKeyService->apiKey() => $apiKey,
            $this->clientApiKeyService->apiSecret() => Hash::make($apiSecret), // Store hashed secret
            $this->clientApiKeyService->keyType() => $type,
            $this->clientApiKeyService->permissions() => $permissions,
            $this->clientApiKeyService->rateLimit() => $this->client->api_rate_limit_default,
            $this->clientApiKeyService->rateLimitPerDay() => $this->client->api_daily_limit_default,
            $this->clientApiKeyService->createdBy() => Auth::id()
        ]);
    }

    /**
     * Validate API key and secret
     */
    public function validateApiKey(string $apiKey, ?string $apiSecret = null): ?ClientApiKey
    {
        $key = $this->clientApiKeyService->query()
            ->where($this->clientApiKeyService->apiKey(), $apiKey)
            ->where($this->clientApiKeyService->status(), StatusEnum::ACTIVE->value)
            ->where(function ($query) {
                $query->whereNull($this->clientApiKeyService->expiresAt())
                    ->orWhere($this->clientApiKeyService->expiresAt(), '>', now());
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
        $this->clientApiKeyService->update($key->id, [
            $this->clientApiKeyService->lastUsedAt() => now(),
            $this->clientApiKeyService->lastUsedIp() => request()->ip(),
            $this->clientApiKeyService->totalRequests() => $key->total_requests + 1
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
        $this->clientApiLogService->create([
            $this->clientApiLogService->clientId() => $this->client->id,
            $this->clientApiLogService->apiKeyId() => $key?->id,
            $this->clientApiLogService->endpoint() => request()->path(),
            $this->clientApiLogService->method() => request()->method(),
            $this->clientApiLogService->requestHeaders() => request()->headers->all(),
            $this->clientApiLogService->requestBody() => request()->all(),
            $this->clientApiLogService->responseCode() => $responseCode,
            $this->clientApiLogService->status() => $status,
            $this->clientApiLogService->errorMessage() => $error,
            $this->clientApiLogService->ipAddress() => request()->ip(),
            $this->clientApiLogService->userAgent() => request()->userAgent(),
            $this->clientApiLogService->responseTimeMs() => $metadata['response_time'] ?? null
        ]);
    }

    /**
     * Increment quota usage
     */
    protected function incrementQuota(ClientApiKey $key): void
    {
        $today = now()->startOfDay();
        $quota = $this->clientApiQuotaService->query()->firstOrCreate(
            [
                $this->clientApiQuotaService->clientId() => $this->client->id,
                $this->clientApiQuotaService->periodStart() => $today,
                $this->clientApiQuotaService->periodEnd() => $today->copy()->endOfDay()
            ],
            [
                $this->clientApiQuotaService->requestsLimit() => $key->rate_limit_per_day,
                $this->clientApiQuotaService->requestsUsed() => 0,
                $this->clientApiQuotaService->resetAt() => $today->copy()->addDay()
            ]
        );

        $quota->increment($this->clientApiQuotaService->requestsUsed());
    }

    /**
     * Revoke API key
     */
    public function revokeApiKey(ClientApiKey $key, string $reason = null): void
    {
        $this->clientApiKeyService->update($key->id, [
            $this->clientApiKeyService->status() => 'revoked',
            'deleted_at' => now()
        ]);

        // Log revocation
        // activity()
        //     ->performedOn($key)
        //     ->withProperties(['reason' => $reason])
        //     ->log('API key revoked');
    }
}
