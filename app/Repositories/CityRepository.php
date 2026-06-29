<?php

namespace App\Repositories;

use App\Models\City;

class CityRepository extends BaseRepository
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function stateId()
    {
        return City::STATE_ID;
    }

    public function countryId()
    {
        return City::COUNTRY_ID;
    }

    public function name()
    {
        return City::NAME;
    }

    public function localName()
    {
        return City::LOCAL_NAME;
    }

    public function district()
    {
        return City::DISTRICT;
    }

    public function latitude()
    {
        return City::LATITUDE;
    }

    public function longitude()
    {
        return City::LONGITUDE;
    }

    public function postalCode()
    {
        return City::POSTAL_CODE;
    }

    public function postalCodes()
    {
        return City::POSTAL_CODES;
    }

    public function timezone()
    {
        return City::TIMEZONE;
    }

    public function isCapital()
    {
        return City::IS_CAPITAL;
    }

    public function isActive()
    {
        return City::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return City::DISPLAY_ORDER;
    }

    public function createdBy()
    {
        return City::CREATED_BY;
    }

    public function updatedBy()
    {
        return City::UPDATED_BY;
    }

    // functions
    public function getByState($state)
    {
        return $this->model->where($this->stateId(), $state)->get();
    }
}