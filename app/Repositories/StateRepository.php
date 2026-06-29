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
        return State::COUNTRY_ID;
    }

    public function name()
    {
        return State::NAME;
    }

    public function code()
    {
        return State::CODE;
    }

    public function type()
    {
        return State::TYPE;
    }

    public function capital()
    {
        return State::CAPITAL;
    }

    public function latitude()
    {
        return State::LATITUDE;
    }

    public function longitude()
    {
        return State::LONGITUDE;
    }

    public function areaKm2()
    {
        return State::AREA_KM2;
    }

    public function population()
    {
        return State::POPULATION;
    }

    public function isActive()
    {
        return State::IS_ACTIVE;
    }

    public function displayOrder()
    {
        return State::DISPLAY_ORDER;
    }

    public function createdBy()
    {
        return State::CREATED_BY;
    }

    public function updatedBy()
    {
        return State::UPDATED_BY;
    }

    // functions
    public function getByCountry($country)
    {
        return $this->model->where($this->countryId(), $country)->get();
    }
}
