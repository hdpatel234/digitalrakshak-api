<?php

namespace App\Repositories;

use App\Models\ProviderCost;

class ProviderCostRepository extends BaseRepository
{
    public function __construct(ProviderCost $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function costPerCall()
    {
        return $this->model::COST_PER_CALL;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function billingModel()
    {
        return $this->model::BILLING_MODEL;
    }

    public function minimumCommitment()
    {
        return $this->model::MINIMUM_COMMITMENT;
    }

    public function commitmentPeriod()
    {
        return $this->model::COMMITMENT_PERIOD;
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
    // functions
}