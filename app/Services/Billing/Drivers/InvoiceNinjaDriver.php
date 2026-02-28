<?php

namespace App\Services\Billing\Drivers;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class InvoiceNinjaDriver extends AbstractBillingDriver
{
    public function createInvoice(array $payload): array
    {
        return $this->request('post', '/invoices', $payload);
    }

    public function getInvoice(string $externalInvoiceId): array
    {
        return $this->request('get', '/invoices/' . $externalInvoiceId);
    }

    public function recordPayment(array $payload): array
    {
        return $this->request('post', '/payments', $payload);
    }

    public function voidInvoice(string $externalInvoiceId, array $payload = []): array
    {
        return $this->request('put', '/invoices/' . $externalInvoiceId, $payload + [
            'status_id' => 5,
        ]);
    }

    public function syncInvoiceStatus(string $externalInvoiceId): array
    {
        return $this->getInvoice($externalInvoiceId);
    }

    protected function request(string $method, string $path, array $payload = []): array
    {
        $baseUrl = rtrim((string) $this->requireConfig('api_url'), '/');
        $token = (string) $this->requireConfig('api_token');
        $timeout = (int) $this->additionalConfig('timeout', 30);

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->withToken($token)
            ->{$method}($baseUrl . '/api/v1' . $path, $payload);

        if ($response->failed()) {
            throw new RuntimeException('Invoice Ninja API request failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }
}

