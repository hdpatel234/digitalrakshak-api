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
        return $this->model::CLIENT_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function customPrice()
    {
        return $this->model::CUSTOM_PRICE;
    }

    public function effectiveFrom()
    {
        return $this->model::EFFECTIVE_FROM;
    }

    public function effectiveTo()
    {
        return $this->model::EFFECTIVE_TO;
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

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}