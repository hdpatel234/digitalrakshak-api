<?php

namespace App\Services\Billing\Drivers;

use App\Models\BillingConfig;
use InvalidArgumentException;

abstract class AbstractBillingDriver
{
    public function __construct(protected BillingConfig $billingConfig) {}

    abstract public function getClientByEmail(string $email): ?array;

    abstract public function createClient(array $payload): array;

    abstract public function getProductByKey(string $productKey): ?array;

    abstract public function createProduct(array $payload): array;

    abstract public function createInvoice(array $payload): array;

    abstract public function getInvoice(string $externalInvoiceId): array;

    abstract public function recordPayment(array $payload): array;

    abstract public function voidInvoice(string $externalInvoiceId, array $payload = []): array;

    abstract public function syncInvoiceStatus(string $externalInvoiceId): array;

    abstract public function downloadInvoice(string $externalInvoiceId): string;

    protected function requireConfig(string $key): mixed
    {
        $value = $this->billingConfig->{$key} ?? null;

        if (blank($value)) {
            throw new InvalidArgumentException("Billing configuration value [$key] is required.");
        }

        return $value;
    }

    protected function additionalConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->billingConfig->additional_config, $key, $default);
    }
}
