<?php

namespace App\Services;

use App\Repositories\SocialLoginAttemptRepository;

class SocialLoginAttemptService extends BaseService
{
    protected $repository;
    
    public function __construct(SocialLoginAttemptRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function email()
    {
        return $this->repository->email();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }

    public function userAgent()
    {
        return $this->repository->userAgent();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }
    // functions
}