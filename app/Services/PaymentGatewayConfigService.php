<?php

namespace App\Services;

use App\Repositories\PaymentGatewayConfigRepository;

/**
 * @property PaymentGatewayConfigRepository $repository
 */
class PaymentGatewayConfigService extends BaseService
{
    public function __construct(PaymentGatewayConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function gatewayId()
    {
        return $this->repository->gatewayId();
    }

    public function configName()
    {
        return $this->repository->configName();
    }

    public function environment()
    {
        return $this->repository->environment();
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

    public function merchantId()
    {
        return $this->repository->merchantId();
    }

    public function merchantKey()
    {
        return $this->repository->merchantKey();
    }

    public function salt()
    {
        return $this->repository->salt();
    }

    public function baseUrl()
    {
        return $this->repository->baseUrl();
    }

    public function webhookUrl()
    {
        return $this->repository->webhookUrl();
    }

    public function callbackUrl()
    {
        return $this->repository->callbackUrl();
    }

    public function redirectUrl()
    {
        return $this->repository->redirectUrl();
    }

    public function enabledMethods()
    {
        return $this->repository->enabledMethods();
    }

    public function currencies()
    {
        return $this->repository->currencies();
    }

    public function minAmount()
    {
        return $this->repository->minAmount();
    }

    public function maxAmount()
    {
        return $this->repository->maxAmount();
    }

    public function transactionFeeType()
    {
        return $this->repository->transactionFeeType();
    }

    public function transactionFeeFixed()
    {
        return $this->repository->transactionFeeFixed();
    }

    public function transactionFeePercentage()
    {
        return $this->repository->transactionFeePercentage();
    }

    public function setupFee()
    {
        return $this->repository->setupFee();
    }

    public function annualFee()
    {
        return $this->repository->annualFee();
    }

    public function settlementCycle()
    {
        return $this->repository->settlementCycle();
    }

    public function settlementBank()
    {
        return $this->repository->settlementBank();
    }

    public function settlementAccount()
    {
        return $this->repository->settlementAccount();
    }
    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
    }

    public function lastCheckedAt()
    {
        return $this->repository->lastCheckedAt();
    }

    public function healthStatus()
    {
        return $this->repository->healthStatus();
    }

    public function errorCount()
    {
        return $this->repository->errorCount();
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
