<?php

namespace App\Repositories;

use App\Models\ClientServicePricing;

class ClientServicePricingRepository extends BaseRepository
{
    public function __construct(ClientServicePricing $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return ClientServicePricing::CLIENT_ID;
    }

    public function serviceId()
    {
        return ClientServicePricing::SERVICE_ID;
    }

    public function customPrice()
    {
        return ClientServicePricing::CUSTOM_PRICE;
    }

    public function effectiveFrom()
    {
        return ClientServicePricing::EFFECTIVE_FROM;
    }

    public function effectiveTo()
    {
        return ClientServicePricing::EFFECTIVE_TO;
    }

    public function status()
    {
        return ClientServicePricing::STATUS;
    }

    public function createdBy()
    {
        return ClientServicePricing::CREATED_BY;
    }

    public function updatedBy()
    {
        return ClientServicePricing::UPDATED_BY;
    }

    public function deletedBy()
    {
        return ClientServicePricing::DELETED_BY;
    }
    // functions
}