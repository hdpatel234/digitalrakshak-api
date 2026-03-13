<?php

namespace App\Repositories;

use App\Models\PaymentGatewayConfig;

class PaymentGatewayConfigRepository extends BaseRepository
{
    public function __construct(PaymentGatewayConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function gatewayId()
    {
        return $this->model::GATEWAY_ID;
    }

    public function configName()
    {
        return $this->model::CONFIG_NAME;
    }

    public function environment()
    {
        return $this->model::ENVIRONMENT;
    }

    public function apiKey()
    {
        return $this->model::API_KEY;
    }

    public function apiSecret()
    {
        return $this->model::API_SECRET;
    }

    public function apiToken()
    {
        return $this->model::API_TOKEN;
    }

    public function merchantId()
    {
        return $this->model::MERCHANT_ID;
    }

    public function merchantKey()
    {
        return $this->model::MERCHANT_KEY;
    }

    public function salt()
    {
        return $this->model::SALT;
    }

    public function baseUrl()
    {
        return $this->model::BASE_URL;
    }

    public function webhookUrl()
    {
        return $this->model::WEBHOOK_URL;
    }

    public function callbackUrl()
    {
        return $this->model::CALLBACK_URL;
    }

    public function redirectUrl()
    {
        return $this->model::REDIRECT_URL;
    }

    public function enabledMethods()
    {
        return $this->model::ENABLED_METHODS;
    }

    public function currencies()
    {
        return $this->model::CURRENCIES;
    }

    public function minAmount()
    {
        return $this->model::MIN_AMOUNT;
    }

    public function maxAmount()
    {
        return $this->model::MAX_AMOUNT;
    }

    public function transactionFeeType()
    {
        return $this->model::TRANSACTION_FEE_TYPE;
    }

    public function transactionFeeFixed()
    {
        return $this->model::TRANSACTION_FEE_FIXED;
    }

    public function transactionFeePercentage()
    {
        return $this->model::TRANSACTION_FEE_PERCENTAGE;
    }

    public function setupFee()
    {
        return $this->model::SETUP_FEE;
    }

    public function annualFee()
    {
        return $this->model::ANNUAL_FEE;
    }

    public function settlementCycle()
    {
        return $this->model::SETTLEMENT_CYCLE;
    }

    public function settlementBank()
    {
        return $this->model::SETTLEMENT_BANK;
    }

    public function settlementAccount()
    {
        return $this->model::SETTLEMENT_ACCOUNT;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function isSandbox()
    {
        return $this->model::IS_SANDBOX;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
    }

    public function lastCheckedAt()
    {
        return $this->model::LAST_CHECKED_AT;
    }

    public function healthStatus()
    {
        return $this->model::HEALTH_STATUS;
    }

    public function errorCount()
    {
        return $this->model::ERROR_COUNT;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}