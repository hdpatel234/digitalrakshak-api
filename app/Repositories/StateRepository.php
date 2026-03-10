<?php

namespace App\Repositories;

use App\Models\State;

class StateRepository extends BaseRepository
{
    public function __construct(State $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function countryId()
    {
        return $this->model::COUNTRY_ID;
    }

    public function name()
    {
        return $this->model::NAME;
    }

    public function code()
    {
        return $this->model::CODE;
    }

    public function type()
    {
        return $this->model::TYPE;
    }

    public function capital()
    {
        return $this->model::CAPITAL;
    }

    public function latitude()
    {
        return $this->model::LATITUDE;
    }

    public function longitude()
    {
        return $this->model::LONGITUDE;
    }

    public function areaKm2()
    {
        return $this->model::AREA_KM2;
    }

    public function population()
    {
        return $this->model::POPULATION;
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
    public function getByCountry($country)
    {
        return $this->model->where($this->countryId(), $country)->get();
    }
}