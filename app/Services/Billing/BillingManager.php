<?php

namespace App\Services\Billing;

use App\Models\Client;
use App\Models\BillingConfig;
use App\Services\Billing\Drivers\AbstractBillingDriver;

class BillingManager
{
    public function __construct(protected BillingDriverFactory $factory) {}

    public function driver(Client $client, ?BillingConfig $billingConfig = null): AbstractBillingDriver
    {
        return $this->factory->driver($client, $billingConfig);
    }

    public function getClientByEmail(Client $client, string $email, ?BillingConfig $billingConfig = null): ?array
    {
        return $this->driver($client, $billingConfig)->getClientByEmail($email);
    }

    public function createClient(Client $client, array $payload, ?BillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->createClient($payload);
    }

    public function getProductByKey(Client $client, string $productKey, ?BillingConfig $billingConfig = null): ?array
    {
        return $this->driver($client, $billingConfig)->getProductByKey($productKey);
    }

    public function createProduct(Client $client, array $payload, ?BillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->createProduct($payload);
    }

    public function createInvoice(Client $client, array $payload, ?BillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->createInvoice($payload);
    }

    public function getInvoice(Client $client, string $externalInvoiceId, ?BillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->getInvoice($externalInvoiceId);
    }

    public function recordPayment(Client $client, array $payload, ?BillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->recordPayment($payload);
    }

    public function voidInvoice(
        Client $client,
        string $externalInvoiceId,
        array $payload = [],
        ?BillingConfig $billingConfig = null
    ): array {
        return $this->driver($client, $billingConfig)->voidInvoice($externalInvoiceId, $payload);
    }

    public function syncInvoiceStatus(
        Client $client,
        string $externalInvoiceId,
        ?BillingConfig $billingConfig = null
    ): array {
        return $this->driver($client, $billingConfig)->syncInvoiceStatus($externalInvoiceId);
    }

    public function downloadInvoice(
        Client $client,
        string $externalInvoiceId,
        ?BillingConfig $billingConfig = null
    ): string {
        return $this->driver($client, $billingConfig)->downloadInvoice($externalInvoiceId);
    }
}
