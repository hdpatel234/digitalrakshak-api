<?php

namespace App\Repositories;

use App\Models\LocationServicePricing;

class LocationServicePricingRepository extends BaseRepository
{
    public function __construct(LocationServicePricing $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return LocationServicePricing::CLIENT_ID;
    }

    public function serviceId()
    {
        return LocationServicePricing::SERVICE_ID;
    }

    public function countryId()
    {
        return LocationServicePricing::COUNTRY_ID;
    }

    public function stateId()
    {
        return LocationServicePricing::STATE_ID;
    }

    public function cityId()
    {
        return LocationServicePricing::CITY_ID;
    }

    public function priceAdjustmentType()
    {
        return LocationServicePricing::PRICE_ADJUSTMENT_TYPE;
    }

    public function priceAdjustment()
    {
        return LocationServicePricing::PRICE_ADJUSTMENT;
    }

    public function finalPrice()
    {
        return LocationServicePricing::FINAL_PRICE;
    }

    public function effectiveFrom()
    {
        return LocationServicePricing::EFFECTIVE_FROM;
    }

    public function effectiveTo()
    {
        return LocationServicePricing::EFFECTIVE_TO;
    }

    public function isActive()
    {
        return LocationServicePricing::IS_ACTIVE;
    }

    public function createdBy()
    {
        return LocationServicePricing::CREATED_BY;
    }
    // functions
}