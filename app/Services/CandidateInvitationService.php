<?php

namespace App\Services;

use App\Repositories\CandidateInvitationRepository;

class CandidateInvitationService extends BaseService
{
    protected $repository;
    
    public function __construct(CandidateInvitationRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function packageId()
    {
        return $this->repository->packageId();
    }

    public function invitationType()
    {
        return $this->repository->invitationType();
    }

    public function invitationToken()
    {
        return $this->repository->invitationToken();
    }

    public function formLink()
    {
        return $this->repository->formLink();
    }

    public function formData()
    {
        return $this->repository->formData();
    }

    public function invitedBy()
    {
        return $this->repository->invitedBy();
    }

    public function invitedAt()
    {
        return $this->repository->invitedAt();
    }

    public function viewedAt()
    {
        return $this->repository->viewedAt();
    }

    public function reminderSentAt()
    {
        return $this->repository->reminderSentAt();
    }

    public function expiresAt()
    {
        return $this->repository->expiresAt();
    }

    public function reminderCount()
    {
        return $this->repository->reminderCount();
    }

    public function lastReminderSentAt()
    {
        return $this->repository->lastReminderSentAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
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