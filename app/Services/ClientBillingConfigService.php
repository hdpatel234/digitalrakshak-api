<?php

namespace App\Services;

use App\Repositories\ClientBillingConfigRepository;

class ClientBillingConfigService extends BaseService
{
    protected $repository;
    
    public function __construct(ClientBillingConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function billingPlatformId()
    {
        return $this->repository->billingPlatformId();
    }

    public function configName()
    {
        return $this->repository->configName();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function apiUrl()
    {
        return $this->repository->apiUrl();
    }

    public function apiKey()
    {
        return $this->repository->apiKey();
    }

    public function apiSecret()
    {
        return $this->repository->apiSecret();
    }

    public function apiToken()
    {
        return $this->repository->apiToken();
    }

    public function webhookSecret()
    {
        return $this->repository->webhookSecret();
    }

    public function additionalConfig()
    {
        return $this->repository->additionalConfig();
    }

    public function invoicePrefix()
    {
        return $this->repository->invoicePrefix();
    }

    public function invoiceSeries()
    {
        return $this->repository->invoiceSeries();
    }

    public function taxRate()
    {
        return $this->repository->taxRate();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function paymentTermsDays()
    {
        return $this->repository->paymentTermsDays();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}