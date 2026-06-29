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
        return ClientServiceArea::CLIENT_ID;
    }

    public function countryId()
    {
        return ClientServiceArea::COUNTRY_ID;
    }

    public function stateId()
    {
        return ClientServiceArea::STATE_ID;
    }

    public function cityId()
    {
        return ClientServiceArea::CITY_ID;
    }

    public function serviceType()
    {
        return ClientServiceArea::SERVICE_TYPE;
    }

    public function isActive()
    {
        return ClientServiceArea::IS_ACTIVE;
    }

    public function createdBy()
    {
        return ClientServiceArea::CREATED_BY;
    }
    // functions
}