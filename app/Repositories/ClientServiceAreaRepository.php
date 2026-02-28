<?php

namespace App\Repositories;

use App\Models\ClientServiceArea;

class ClientServiceAreaRepository extends BaseRepository
{
    public function __construct(ClientServiceArea $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
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

    public function serviceType()
    {
        return $this->model::SERVICE_TYPE;
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