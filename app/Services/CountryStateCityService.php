<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class CountryStateCityService
{
    protected $apiKeys;
    protected $baseUrl;
    protected $dailyLimit = 100;

    public function __construct()
    {
        $this->apiKeys = config('countrystatecity.api_keys', []);
        $this->baseUrl = config('countrystatecity.base_url');
    }

    /**
     * Get an available API key that hasn't reached its daily limit.
     */
    protected function getAvailableApiKey()
    {
        if (empty($this->apiKeys)) {
            throw new Exception("No CountryStateCity API keys configured.");
        }

        $date = now()->format('Y_m_d');

        foreach ($this->apiKeys as $key) {
            $cacheKey = "csc_api_usage_{$key}_{$date}";
            $usage = Cache::get($cacheKey, 0);

            if ($usage < $this->dailyLimit) {
                return $key;
            }
        }

        throw new Exception("All CountryStateCity API keys have reached their daily limit of {$this->dailyLimit} requests.");
    }

    /**
     * Increment the usage counter for an API key.
     */
    protected function incrementApiKeyUsage($key)
    {
        $date = now()->format('Y_m_d');
        $cacheKey = "csc_api_usage_{$key}_{$date}";
        
        // Cache for 24 hours (86400 seconds)
        $usage = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $usage + 1, 86400);
    }

    /**
     * Make an API request to the given endpoint.
     */
    protected function makeRequest($endpoint)
    {
        $apiKey = $this->getAvailableApiKey();
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $response = Http::withHeaders([
            'X-CSCAPI-KEY' => $apiKey,
        ])->get($url);

        $this->incrementApiKeyUsage($apiKey);

        if ($response->successful()) {
            return $response->json();
        }

        // If the request fails due to rate limit (429), we might want to forcefully max out the key and retry.
        if ($response->status() === 429 || $response->status() === 401) {
            // Force this key to be marked as exhausted for today
            $date = now()->format('Y_m_d');
            $cacheKey = "csc_api_usage_{$apiKey}_{$date}";
            Cache::put($cacheKey, $this->dailyLimit, 86400);
            
            // Retry with next available key
            return $this->makeRequest($endpoint);
        }

        Log::error("CountryStateCity API error: " . $response->body());
        throw new Exception("Failed to fetch data from CountryStateCity API. Status: " . $response->status());
    }

    public function getCountries()
    {
        return $this->makeRequest('/countries');
    }

    public function getStates($countryCode)
    {
        return $this->makeRequest("/countries/{$countryCode}/states");
    }

    public function getCities($countryCode, $stateCode)
    {
        return $this->makeRequest("/countries/{$countryCode}/states/{$stateCode}/cities");
    }
}
