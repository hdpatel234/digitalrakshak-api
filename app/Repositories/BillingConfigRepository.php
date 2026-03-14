<?php

namespace App\Repositories;

use App\Models\BillingConfig;

class BillingConfigRepository extends BaseRepository
{
    public function __construct(BillingConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function billingPlatformId()
    {
        return $this->model::BILLING_PLATFORM_ID;
    }

    public function configName()
    {
        return $this->model::CONFIG_NAME;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function apiUrl()
    {
        return $this->model::API_URL;
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

    public function webhookSecret()
    {
        return $this->model::WEBHOOK_SECRET;
    }

    public function additionalConfig()
    {
        return $this->model::ADDITIONAL_CONFIG;
    }

    public function invoicePrefix()
    {
        return $this->model::INVOICE_PREFIX;
    }

    public function invoiceSeries()
    {
        return $this->model::INVOICE_SERIES;
    }

    public function taxRate()
    {
        return $this->model::TAX_RATE;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function paymentTermsDays()
    {
        return $this->model::PAYMENT_TERMS_DAYS;
    }

    public function status()
    {
        return $this->model::STATUS;
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