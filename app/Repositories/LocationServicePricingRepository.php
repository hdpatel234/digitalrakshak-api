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
        return $this->model::CLIENT_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function countryId()
    {
        return $this->model::COUNTRY_ID;
    }

    public function stateId()
    {
        return $this->model::STATE_ID;
    }

    public function cityId()
    {
        return $this->model::CITY_ID;
    }

    public function priceAdjustmentType()
    {
        return $this->model::PRICE_ADJUSTMENT_TYPE;
    }

    public function priceAdjustment()
    {
        return $this->model::PRICE_ADJUSTMENT;
    }

    public function finalPrice()
    {
        return $this->model::FINAL_PRICE;
    }

    public function effectiveFrom()
    {
        return $this->model::EFFECTIVE_FROM;
    }

    public function effectiveTo()
    {
        return $this->model::EFFECTIVE_TO;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }
    // functions
}