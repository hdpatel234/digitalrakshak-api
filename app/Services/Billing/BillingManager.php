<?php

namespace App\Services\Billing;

use App\Models\Client;
use App\Models\ClientBillingConfig;
use App\Services\Billing\Drivers\AbstractBillingDriver;

class BillingManager
{
    public function __construct(protected BillingDriverFactory $factory)
    {
    }

    public function driver(Client $client, ?ClientBillingConfig $billingConfig = null): AbstractBillingDriver
    {
        return $this->factory->driver($client, $billingConfig);
    }

    public function createInvoice(Client $client, array $payload, ?ClientBillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->createInvoice($payload);
    }

    public function getInvoice(Client $client, string $externalInvoiceId, ?ClientBillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->getInvoice($externalInvoiceId);
    }

    public function recordPayment(Client $client, array $payload, ?ClientBillingConfig $billingConfig = null): array
    {
        return $this->driver($client, $billingConfig)->recordPayment($payload);
    }

    public function voidInvoice(
        Client $client,
        string $externalInvoiceId,
        array $payload = [],
        ?ClientBillingConfig $billingConfig = null
    ): array {
        return $this->driver($client, $billingConfig)->voidInvoice($externalInvoiceId, $payload);
    }

    public function syncInvoiceStatus(
        Client $client,
        string $externalInvoiceId,
        ?ClientBillingConfig $billingConfig = null
    ): array {
        return $this->driver($client, $billingConfig)->syncInvoiceStatus($externalInvoiceId);
    }
}

