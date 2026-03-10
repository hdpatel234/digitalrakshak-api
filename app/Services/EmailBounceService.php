<?php

namespace App\Services;

use App\Repositories\EmailBounceRepository;

class EmailBounceService extends BaseService
{
    
    public function __construct(EmailBounceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function email()
    {
        return $this->repository->email();
    }

    public function bounceType()
    {
        return $this->repository->bounceType();
    }

    public function reason()
    {
        return $this->repository->reason();
    }

    public function bouncedAt()
    {
        return $this->repository->bouncedAt();
    }

    public function unsubscribedAt()
    {
        return $this->repository->unsubscribedAt();
    }

    public function blockedUntil()
    {
        return $this->repository->blockedUntil();
    }

    public function bounceCount()
    {
        return $this->repository->bounceCount();
    }
    // functions
}