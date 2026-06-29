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
        return ProviderCost::PROVIDER_ID;
    }

    public function serviceId()
    {
        return ProviderCost::SERVICE_ID;
    }

    public function costPerCall()
    {
        return ProviderCost::COST_PER_CALL;
    }

    public function currency()
    {
        return ProviderCost::CURRENCY;
    }

    public function billingModel()
    {
        return ProviderCost::BILLING_MODEL;
    }

    public function minimumCommitment()
    {
        return ProviderCost::MINIMUM_COMMITMENT;
    }

    public function commitmentPeriod()
    {
        return ProviderCost::COMMITMENT_PERIOD;
    }

    public function effectiveFrom()
    {
        return ProviderCost::EFFECTIVE_FROM;
    }

    public function effectiveTo()
    {
        return ProviderCost::EFFECTIVE_TO;
    }

    public function isActive()
    {
        return ProviderCost::IS_ACTIVE;
    }
    // functions
}