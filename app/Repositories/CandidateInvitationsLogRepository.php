<?php

namespace App\Repositories;

use App\Models\CandidateInvitationsLog;

class CandidateInvitationsLogRepository extends BaseRepository
{
    public function __construct(CandidateInvitationsLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function invitationId()
    {
        return $this->model::INVITATION_ID;
    }

    public function action()
    {
        return $this->model::ACTION;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }

    public function userAgent()
    {
        return $this->model::USER_AGENT;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}