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
        return CandidateInvitationsLog::INVITATION_ID;
    }

    public function action()
    {
        return CandidateInvitationsLog::ACTION;
    }

    public function ipAddress()
    {
        return CandidateInvitationsLog::IP_ADDRESS;
    }

    public function userAgent()
    {
        return CandidateInvitationsLog::USER_AGENT;
    }

    public function status()
    {
        return CandidateInvitationsLog::STATUS;
    }

    public function createdBy()
    {
        return CandidateInvitationsLog::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateInvitationsLog::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateInvitationsLog::DELETED_BY;
    }
    // functions
}