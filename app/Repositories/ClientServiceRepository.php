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
}