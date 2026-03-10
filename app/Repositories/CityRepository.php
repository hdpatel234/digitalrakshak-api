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
        return $this->model::STATE_ID;
    }

    public function countryId()
    {
        return $this->model::COUNTRY_ID;
    }

    public function name()
    {
        return $this->model::NAME;
    }

    public function localName()
    {
        return $this->model::LOCAL_NAME;
    }

    public function district()
    {
        return $this->model::DISTRICT;
    }

    public function latitude()
    {
        return $this->model::LATITUDE;
    }

    public function longitude()
    {
        return $this->model::LONGITUDE;
    }

    public function postalCode()
    {
        return $this->model::POSTAL_CODE;
    }

    public function postalCodes()
    {
        return $this->model::POSTAL_CODES;
    }

    public function timezone()
    {
        return $this->model::TIMEZONE;
    }

    public function isCapital()
    {
        return $this->model::IS_CAPITAL;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    // functions
    public function getByState($state)
    {
        return $this->model->where($this->stateId(), $state)->get();
    }
}