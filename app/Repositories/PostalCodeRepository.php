<?php

namespace App\Repositories;

use App\Models\PostalCode;

class PostalCodeRepository extends BaseRepository
{
    public function __construct(PostalCode $model)
    {
        parent::__construct($model);
    }

    // column constants
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

    public function postalCode()
    {
        return $this->model::POSTAL_CODE;
    }

    public function latitude()
    {
        return $this->model::LATITUDE;
    }

    public function longitude()
    {
        return $this->model::LONGITUDE;
    }

    public function accuracy()
    {
        return $this->model::ACCURACY;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
}