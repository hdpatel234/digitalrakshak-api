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
        return $this->model::CLIENT_ID;
    }

    public function gatewayConfigId()
    {
        return $this->model::GATEWAY_CONFIG_ID;
    }

    public function displayName()
    {
        return $this->model::DISPLAY_NAME;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function enabledMethods()
    {
        return $this->model::ENABLED_METHODS;
    }

    public function currencies()
    {
        return $this->model::CURRENCIES;
    }

    public function feeType()
    {
        return $this->model::FEE_TYPE;
    }

    public function feeFixed()
    {
        return $this->model::FEE_FIXED;
    }

    public function feePercentage()
    {
        return $this->model::FEE_PERCENTAGE;
    }

    public function minAmount()
    {
        return $this->model::MIN_AMOUNT;
    }

    public function maxAmount()
    {
        return $this->model::MAX_AMOUNT;
    }

    public function dailyLimit()
    {
        return $this->model::DAILY_LIMIT;
    }

    public function monthlyLimit()
    {
        return $this->model::MONTHLY_LIMIT;
    }

    public function isEnabled()
    {
        return $this->model::IS_ENABLED;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function isMandatory()
    {
        return $this->model::IS_MANDATORY;
    }

    public function totalTransactions()
    {
        return $this->model::TOTAL_TRANSACTIONS;
    }

    public function totalAmount()
    {
        return $this->model::TOTAL_AMOUNT;
    }

    public function lastTransactionAt()
    {
        return $this->model::LAST_TRANSACTION_AT;
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