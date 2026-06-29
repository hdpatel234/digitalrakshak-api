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
        return Country::NAME;
    }

    public function isoCode2()
    {
        return Country::ISO_CODE_2;
    }

    public function isoCode3()
    {
        return Country::ISO_CODE_3;
    }

    public function numericCode()
    {
        return Country::NUMERIC_CODE;
    }

    public function phoneCode()
    {
        return Country::PHONE_CODE;
    }

    public function currencyCode()
    {
        return Country::CURRENCY_CODE;
    }

    public function currencySymbol()
    {
        return Country::CURRENCY_SYMBOL;
    }

    public function capital()
    {
        return Country::CAPITAL;
    }

    public function continent()
    {
        return Country::CONTINENT;
    }

    public function flagIcon()
    {
        return Country::FLAG_ICON;
    }

    public function flagImage()
    {
        return Country::FLAG_IMAGE;
    }

    public function latitude()
    {
        return Country::LATITUDE;
    }

    public function longitude()
    {
        return Country::LONGITUDE;
    }

    public function timezones()
    {
        return Country::TIMEZONES;
    }

    public function postalCodeFormat()
    {
        return Country::POSTAL_CODE_FORMAT;
    }

    public function postalCodeRegex()
    {
        return Country::POSTAL_CODE_REGEX;
    }

    public function isActive()
    {
        return Country::IS_ACTIVE;
    }

    public function isDefault()
    {
        return Country::IS_DEFAULT;
    }

    public function displayOrder()
    {
        return Country::DISPLAY_ORDER;
    }

    public function createdBy()
    {
        return Country::CREATED_BY;
    }

    public function updatedBy()
    {
        return Country::UPDATED_BY;
    }
    // functions
}