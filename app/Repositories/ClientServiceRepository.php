<?php

namespace App\Repositories;

use App\Models\ClientService;

class ClientServiceRepository extends BaseRepository
{
    public function __construct(ClientService $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return ClientService::CLIENT_ID;
    }

    public function serviceId()
    {
        return ClientService::SERVICE_ID;
    }

    public function status()
    {
        return ClientService::STATUS;
    }

    public function createdBy()
    {
        return ClientService::CREATED_BY;
    }

    public function updatedBy()
    {
        return ClientService::UPDATED_BY;
    }

    public function deletedBy()
    {
        return ClientService::DELETED_BY;
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
    
    public function updateStatusNotInList(int $clientId, array $serviceIds, string $status)
    {
        $query = $this->query()->where($this->clientId(), $clientId);
        if (!empty($serviceIds)) {
            $query->whereNotIn($this->serviceId(), $serviceIds);
        }
        return $query->update([$this->status() => $status]);
    }
    
    public function getByClientId(int $clientId)
    {
        return $this->query()->where($this->clientId(), $clientId)->get()->keyBy($this->serviceId());
    }
}
