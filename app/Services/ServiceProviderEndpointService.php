<?php

namespace App\Services;

use App\Repositories\ServiceProviderEndpointRepository;

class ServiceProviderEndpointService
{
    public function __construct(protected ServiceProviderEndpointRepository $serviceProviderEndpointRepository) {}

    public function getEndpoint(string $apiCode, int $configId)
    {
        return $this->serviceProviderEndpointRepository->getEndpointByCode($apiCode, $configId);
    }

    public function buildUrl($endpoint)
    {
        if (!$endpoint || !$endpoint->config) {
            return '';
        }

        $baseUrl = rtrim($endpoint->config->base_url, '/');
        $path = ltrim($endpoint->endpoint_path, '/');

        return $baseUrl . '/' . $path;
    }

    public function getHeaders($endpoint)
    {
        if (!$endpoint) {
            return [];
        }

        $headers = [];

        // Add config default headers
        if ($endpoint->config && $endpoint->config->default_headers) {
            $configHeaders = is_string($endpoint->config->default_headers)
                ? json_decode($endpoint->config->default_headers, true)
                : $endpoint->config->default_headers;

            if (is_array($configHeaders)) {
                $headers = array_merge($headers, $configHeaders);
            }
        }

        if ($endpoint->custom_headers) {
            $customHeaders = is_string($endpoint->custom_headers)
                ? json_decode($endpoint->custom_headers, true)
                : $endpoint->custom_headers;

            if (is_array($customHeaders)) {
                $headers = array_merge($headers, $customHeaders);
            }
        }

        if ($endpoint->content_type) {
            $headers['Content-Type'] = $endpoint->content_type;
        }

        return $headers;
    }
}
