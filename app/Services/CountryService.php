<?php

namespace App\Services;

use App\Repositories\CountryRepository;

/**
 * @property CountryRepository $repository
 */
class CountryService extends BaseService
{
    
    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function name()
    {
        return $this->repository->name();
    }

    public function isoCode2()
    {
        return $this->repository->isoCode2();
    }

    public function isoCode3()
    {
        return $this->repository->isoCode3();
    }

    public function numericCode()
    {
        return $this->repository->numericCode();
    }

    public function phoneCode()
    {
        return $this->repository->phoneCode();
    }

    public function currencyCode()
    {
        return $this->repository->currencyCode();
    }

    public function currencySymbol()
    {
        return $this->repository->currencySymbol();
    }

    public function capital()
    {
        return $this->repository->capital();
    }

    public function continent()
    {
        return $this->repository->continent();
    }

    public function flagIcon()
    {
        return $this->repository->flagIcon();
    }

    public function flagImage()
    {
        return $this->repository->flagImage();
    }

    public function latitude()
    {
        return $this->repository->latitude();
    }

    public function longitude()
    {
        return $this->repository->longitude();
    }

    public function timezones()
    {
        return $this->repository->timezones();
    }

    public function postalCodeFormat()
    {
        return $this->repository->postalCodeFormat();
    }

    public function postalCodeRegex()
    {
        return $this->repository->postalCodeRegex();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}
