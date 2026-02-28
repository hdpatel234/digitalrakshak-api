<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository extends BaseRepository
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function name()
    {
        return $this->model::NAME;
    }

    public function isoCode2()
    {
        return $this->model::ISO_CODE_2;
    }

    public function isoCode3()
    {
        return $this->model::ISO_CODE_3;
    }

    public function numericCode()
    {
        return $this->model::NUMERIC_CODE;
    }

    public function phoneCode()
    {
        return $this->model::PHONE_CODE;
    }

    public function currencyCode()
    {
        return $this->model::CURRENCY_CODE;
    }

    public function currencySymbol()
    {
        return $this->model::CURRENCY_SYMBOL;
    }

    public function capital()
    {
        return $this->model::CAPITAL;
    }

    public function continent()
    {
        return $this->model::CONTINENT;
    }

    public function flagIcon()
    {
        return $this->model::FLAG_ICON;
    }

    public function flagImage()
    {
        return $this->model::FLAG_IMAGE;
    }

    public function latitude()
    {
        return $this->model::LATITUDE;
    }

    public function longitude()
    {
        return $this->model::LONGITUDE;
    }

    public function timezones()
    {
        return $this->model::TIMEZONES;
    }

    public function postalCodeFormat()
    {
        return $this->model::POSTAL_CODE_FORMAT;
    }

    public function postalCodeRegex()
    {
        return $this->model::POSTAL_CODE_REGEX;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
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
}