<?php

namespace App\Services;

use App\Repositories\ClientApiQuotaRepository;

/**
 * @property ClientApiQuotaRepository $repository
 */
class ClientApiQuotaService extends BaseService
{
    
    public function __construct(ClientApiQuotaRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function periodStart()
    {
        return $this->repository->periodStart();
    }

    public function periodEnd()
    {
        return $this->repository->periodEnd();
    }

    public function requestsLimit()
    {
        return $this->repository->requestsLimit();
    }

    public function requestsUsed()
    {
        return $this->repository->requestsUsed();
    }

    public function requestsRemaining()
    {
        return $this->repository->requestsRemaining();
    }

    public function resetAt()
    {
        return $this->repository->resetAt();
    }
    // functions
}
