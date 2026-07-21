<?php

namespace App\Services;

use App\Repositories\UserConfigValueRepository;

/**
 * @property UserConfigValueRepository $repository
 */
class UserConfigValueService extends BaseService
{
    
    public function __construct(UserConfigValueRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function configId()
    {
        return $this->repository->configId();
    }

    public function value()
    {
        return $this->repository->value();
    }
    // functions
}
