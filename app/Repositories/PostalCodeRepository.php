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
        return PostalCode::COUNTRY_ID;
    }

    public function stateId()
    {
        return PostalCode::STATE_ID;
    }

    public function cityId()
    {
        return PostalCode::CITY_ID;
    }

    public function postalCode()
    {
        return PostalCode::POSTAL_CODE;
    }

    public function latitude()
    {
        return PostalCode::LATITUDE;
    }

    public function longitude()
    {
        return PostalCode::LONGITUDE;
    }

    public function accuracy()
    {
        return PostalCode::ACCURACY;
    }

    public function isActive()
    {
        return PostalCode::IS_ACTIVE;
    }
    // functions
}