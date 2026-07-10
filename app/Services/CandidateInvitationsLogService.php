<?php

namespace App\Services;

use App\Repositories\CandidateInvitationsLogRepository;

/**
 * @property CandidateInvitationsLogRepository $repository
 */
class CandidateInvitationsLogService extends BaseService
{
    
    public function __construct(CandidateInvitationsLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function invitationId()
    {
        return $this->repository->invitationId();
    }

    public function action()
    {
        return $this->repository->action();
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

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    // functions
}