<?php

namespace App\Services\Billing\Drivers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class InvoiceNinjaDriver extends AbstractBillingDriver
{
    public function getClientByEmail(string $email): ?array
    {
        $response = $this->request('get', '/clients', [
            'email' => $email,
        ]);

        $clients = $response['data'] ?? [];
        if (!empty($clients)) {
            return $clients[0];
        }

        return null;
    }

    public function createClient(array $payload): array
    {
        return $this->request('post', '/clients', $payload);
    }

    public function getProductByKey(string $productKey): ?array
    {
        $response = $this->request('get', '/products', [
            'filter' => $productKey,
        ]);

        $products = $response['data'] ?? [];
        foreach ($products as $product) {
            if (($product['product_key'] ?? '') === $productKey) {
                return $product;
            }
        }

        return null;
    }

    public function createProduct(array $payload): array
    {
        return $this->request('post', '/products', $payload);
    }

    public function createInvoice(array $payload): array
    {
        return $this->request('post', '/invoices?mark_sent=true', $payload);
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

    public function downloadInvoice(string $externalInvoiceId): string
    {
        $baseUrl = rtrim((string) $this->requireConfig('api_url'), '/');
        $token = (string) $this->requireConfig('api_token');
        $timeout = (int) $this->additionalConfig('timeout', 30);

        $response = Http::timeout($timeout)
            ->accept('application/pdf')
            ->withHeaders([
                'X-API-TOKEN' => $token,
            ])
            ->get("{$baseUrl}/invoices/{$externalInvoiceId}/download");

        Log::debug('Invoice Ninja API request (Download): Method: get | Path: ' . $baseUrl . '/invoices/' . $externalInvoiceId . '/download' . ' | Response length: ' . strlen($response->body()));

        if ($response->failed()) {
            throw new RuntimeException('Invoice Ninja API request failed: ' . $response->body());
        }

        return $response->body();
    }

    protected function request(string $method, string $path, array $payload = []): array
    {
        $baseUrl = rtrim((string) $this->requireConfig('api_url'), '/');
        $token = (string) $this->requireConfig('api_token');
        $timeout = (int) $this->additionalConfig('timeout', 30);

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders([
                'X-API-TOKEN' => $token,
            ])
            ->{$method}($baseUrl . $path, $payload);

        Log::debug('Invoice Ninja API request: Method: ' . $method . ' | Path: ' . $baseUrl . '' . $path . ' | Payload: ' . json_encode($payload) . ' | Response: ' . $response->body());

        if ($response->failed()) {
            throw new RuntimeException('Invoice Ninja API request failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }

    public function getInvoiceNinjaPaymentTypeId(string $methodName): string
    {
        $normalized = strtolower(trim($methodName));
        if ($normalized === '') {
            return '1'; // Default: Bank Transfer
        }

        $map = [
            'bank transfer' => '1',
            'cash' => '2',
            'debit' => '3',
            'ach' => '4',
            'visa' => '5',
            'mastercard' => '6',
            'american express' => '7',
            'discover' => '8',
            'diners card' => '9',
            'eurocard' => '10',
            'nova' => '11',
            'credit card other' => '12',
            'credit card' => '12',
            'paypal' => '13',
            'google wallet' => '14',
            'check' => '15',
            'carte blanche' => '16',
            'unionpay' => '17',
            'jcb' => '18',
            'laser' => '19',
            'maestro' => '20',
            'solo' => '21',
            'switch' => '22',
            'izettle' => '23',
            'swish' => '24',
            'venmo' => '25',
            'money order' => '26',
            'alipay' => '27',
            'sofort' => '28',
            'sepa direct debit' => '29',
            'sepa' => '29',
            'gocardless' => '30',
            'cryptocurrency' => '31',
            'crypto' => '31',
            'credit' => '32',
            'zelle' => '33',
        ];

        foreach ($map as $key => $id) {
            if (str_contains($normalized, $key)) {
                return $id;
            }
        }

        return '1'; // Default fallback
    }
}
