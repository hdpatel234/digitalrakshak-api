<?php

namespace App\Services;

use App\Repositories\PostalCodeRepository;

/**
 * @property PostalCodeRepository $repository
 */
class PostalCodeService extends BaseService
{
    
    public function __construct(PostalCodeRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function countryId()
    {
        return $this->repository->countryId();
    }

    public function stateId()
    {
        return $this->repository->stateId();
    }

    public function cityId()
    {
        return $this->repository->cityId();
    }

    public function postalCode()
    {
        return $this->repository->postalCode();
    }

    public function latitude()
    {
        return $this->repository->latitude();
    }

    public function longitude()
    {
        return $this->repository->longitude();
    }

    public function accuracy()
    {
        return $this->repository->accuracy();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}