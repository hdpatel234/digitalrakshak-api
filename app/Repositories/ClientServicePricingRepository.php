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
    public function updateOrCreateByClientAndService(int $clientId, int $serviceId, array $data)
    {
        return $this->query()->updateOrCreate(
            [$this->clientId() => $clientId, $this->serviceId() => $serviceId],
            $data
        );
    }
    
    public function deleteByClientAndService(int $clientId, int $serviceId)
    {
        return $this->query()
            ->where($this->clientId(), $clientId)
            ->where($this->serviceId(), $serviceId)
            ->delete();
    }
    
    public function getByClientId(int $clientId)
    {
        return $this->query()->where($this->clientId(), $clientId)->get()->keyBy($this->serviceId());
    }
}
