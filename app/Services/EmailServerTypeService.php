<?php

namespace App\Services;

use App\Repositories\EmailServerTypeRepository;

/**
 * @property EmailServerTypeRepository $repository
 */
class EmailServerTypeService extends BaseService
{
    public function __construct(EmailServerTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function typeName()
    {
        return $this->repository->typeName();
    }

    public function typeCode()
    {
        return $this->repository->typeCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function isOutgoing()
    {
        return $this->repository->isOutgoing();
    }

    public function isIncoming()
    {
        return $this->repository->isIncoming();
    }

    // functions
}
