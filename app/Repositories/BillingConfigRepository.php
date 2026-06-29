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
        return BillingConfig::BILLING_PLATFORM_ID;
    }

    public function configName()
    {
        return BillingConfig::CONFIG_NAME;
    }

    public function isDefault()
    {
        return BillingConfig::IS_DEFAULT;
    }

    public function apiUrl()
    {
        return BillingConfig::API_URL;
    }

    public function apiKey()
    {
        return BillingConfig::API_KEY;
    }

    public function apiSecret()
    {
        return BillingConfig::API_SECRET;
    }

    public function apiToken()
    {
        return BillingConfig::API_TOKEN;
    }

    public function webhookSecret()
    {
        return BillingConfig::WEBHOOK_SECRET;
    }

    public function additionalConfig()
    {
        return BillingConfig::ADDITIONAL_CONFIG;
    }

    public function invoicePrefix()
    {
        return BillingConfig::INVOICE_PREFIX;
    }

    public function invoiceSeries()
    {
        return BillingConfig::INVOICE_SERIES;
    }

    public function taxRate()
    {
        return BillingConfig::TAX_RATE;
    }

    public function currency()
    {
        return BillingConfig::CURRENCY;
    }

    public function paymentTermsDays()
    {
        return BillingConfig::PAYMENT_TERMS_DAYS;
    }

    public function status()
    {
        return BillingConfig::STATUS;
    }

    public function createdBy()
    {
        return BillingConfig::CREATED_BY;
    }

    public function updatedBy()
    {
        return BillingConfig::UPDATED_BY;
    }
    // functions
}