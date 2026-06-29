<?php

namespace App\Repositories;

use App\Models\ClientPaymentGateway;

class ClientPaymentGatewayRepository extends BaseRepository
{
    public function __construct(ClientPaymentGateway $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return ClientPaymentGateway::CLIENT_ID;
    }

    public function gatewayConfigId()
    {
        return ClientPaymentGateway::GATEWAY_CONFIG_ID;
    }

    public function displayName()
    {
        return ClientPaymentGateway::DISPLAY_NAME;
    }

    public function displayOrder()
    {
        return ClientPaymentGateway::DISPLAY_ORDER;
    }

    public function enabledMethods()
    {
        return ClientPaymentGateway::ENABLED_METHODS;
    }

    public function currencies()
    {
        return ClientPaymentGateway::CURRENCIES;
    }

    public function feeType()
    {
        return ClientPaymentGateway::FEE_TYPE;
    }

    public function feeFixed()
    {
        return ClientPaymentGateway::FEE_FIXED;
    }

    public function feePercentage()
    {
        return ClientPaymentGateway::FEE_PERCENTAGE;
    }

    public function minAmount()
    {
        return ClientPaymentGateway::MIN_AMOUNT;
    }

    public function maxAmount()
    {
        return ClientPaymentGateway::MAX_AMOUNT;
    }

    public function dailyLimit()
    {
        return ClientPaymentGateway::DAILY_LIMIT;
    }

    public function monthlyLimit()
    {
        return ClientPaymentGateway::MONTHLY_LIMIT;
    }

    public function isEnabled()
    {
        return ClientPaymentGateway::IS_ENABLED;
    }

    public function isDefault()
    {
        return ClientPaymentGateway::IS_DEFAULT;
    }

    public function isMandatory()
    {
        return ClientPaymentGateway::IS_MANDATORY;
    }

    public function totalTransactions()
    {
        return ClientPaymentGateway::TOTAL_TRANSACTIONS;
    }

    public function totalAmount()
    {
        return ClientPaymentGateway::TOTAL_AMOUNT;
    }

    public function lastTransactionAt()
    {
        return ClientPaymentGateway::LAST_TRANSACTION_AT;
    }

    public function createdBy()
    {
        return ClientPaymentGateway::CREATED_BY;
    }

    public function updatedBy()
    {
        return ClientPaymentGateway::UPDATED_BY;
    }
    // functions
}