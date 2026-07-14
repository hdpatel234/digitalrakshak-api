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
        return PaymentGatewayConfig::GATEWAY_ID;
    }

    public function configName()
    {
        return PaymentGatewayConfig::CONFIG_NAME;
    }

    public function environment()
    {
        return PaymentGatewayConfig::ENVIRONMENT;
    }

    public function apiKey()
    {
        return PaymentGatewayConfig::API_KEY;
    }

    public function apiSecret()
    {
        return PaymentGatewayConfig::API_SECRET;
    }

    public function apiToken()
    {
        return PaymentGatewayConfig::API_TOKEN;
    }

    public function merchantId()
    {
        return PaymentGatewayConfig::MERCHANT_ID;
    }

    public function merchantKey()
    {
        return PaymentGatewayConfig::MERCHANT_KEY;
    }

    public function salt()
    {
        return PaymentGatewayConfig::SALT;
    }

    public function baseUrl()
    {
        return PaymentGatewayConfig::BASE_URL;
    }

    public function webhookUrl()
    {
        return PaymentGatewayConfig::WEBHOOK_URL;
    }

    public function callbackUrl()
    {
        return PaymentGatewayConfig::CALLBACK_URL;
    }

    public function redirectUrl()
    {
        return PaymentGatewayConfig::REDIRECT_URL;
    }

    public function enabledMethods()
    {
        return PaymentGatewayConfig::ENABLED_METHODS;
    }

    public function currencies()
    {
        return PaymentGatewayConfig::CURRENCIES;
    }

    public function minAmount()
    {
        return PaymentGatewayConfig::MIN_AMOUNT;
    }

    public function maxAmount()
    {
        return PaymentGatewayConfig::MAX_AMOUNT;
    }

    public function transactionFeeType()
    {
        return PaymentGatewayConfig::TRANSACTION_FEE_TYPE;
    }

    public function transactionFeeFixed()
    {
        return PaymentGatewayConfig::TRANSACTION_FEE_FIXED;
    }

    public function transactionFeePercentage()
    {
        return PaymentGatewayConfig::TRANSACTION_FEE_PERCENTAGE;
    }

    public function setupFee()
    {
        return PaymentGatewayConfig::SETUP_FEE;
    }

    public function annualFee()
    {
        return PaymentGatewayConfig::ANNUAL_FEE;
    }

    public function settlementCycle()
    {
        return PaymentGatewayConfig::SETTLEMENT_CYCLE;
    }

    public function settlementBank()
    {
        return PaymentGatewayConfig::SETTLEMENT_BANK;
    }

    public function settlementAccount()
    {
        return PaymentGatewayConfig::SETTLEMENT_ACCOUNT;
    }

    public function isActive()
    {
        return PaymentGatewayConfig::STATUS;
    }


    public function lastUsedAt()
    {
        return PaymentGatewayConfig::LAST_USED_AT;
    }

    public function lastCheckedAt()
    {
        return PaymentGatewayConfig::LAST_CHECKED_AT;
    }

    public function healthStatus()
    {
        return PaymentGatewayConfig::HEALTH_STATUS;
    }

    public function errorCount()
    {
        return PaymentGatewayConfig::ERROR_COUNT;
    }

    public function createdBy()
    {
        return PaymentGatewayConfig::CREATED_BY;
    }

    public function updatedBy()
    {
        return PaymentGatewayConfig::UPDATED_BY;
    }
    // functions
}
