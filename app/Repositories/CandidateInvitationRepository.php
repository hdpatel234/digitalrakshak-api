<?php

namespace App\Repositories;

use App\Models\CandidateInvitation;

class CandidateInvitationRepository extends BaseRepository
{
    public function __construct(CandidateInvitation $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateId()
    {
        return CandidateInvitation::CANDIDATE_ID;
    }

    public function clientId()
    {
        return CandidateInvitation::CLIENT_ID;
    }

    public function packageId()
    {
        return CandidateInvitation::PACKAGE_ID;
    }

    public function invitationType()
    {
        return CandidateInvitation::INVITATION_TYPE;
    }

    public function invitationToken()
    {
        return CandidateInvitation::INVITATION_TOKEN;
    }

    public function formLink()
    {
        return CandidateInvitation::FORM_LINK;
    }

    public function invitedBy()
    {
        return CandidateInvitation::INVITED_BY;
    }

    public function invitedAt()
    {
        return CandidateInvitation::INVITED_AT;
    }

    public function viewedAt()
    {
        return CandidateInvitation::VIEWED_AT;
    }

    public function reminderSentAt()
    {
        return CandidateInvitation::REMINDER_SENT_AT;
    }

    public function expiresAt()
    {
        return CandidateInvitation::EXPIRES_AT;
    }

    public function reminderCount()
    {
        return CandidateInvitation::REMINDER_COUNT;
    }

    public function lastReminderSentAt()
    {
        return CandidateInvitation::LAST_REMINDER_SENT_AT;
    }

    public function completedAt()
    {
        return CandidateInvitation::COMPLETED_AT;
    }

    public function status()
    {
        return CandidateInvitation::STATUS;
    }

    public function createdBy()
    {
        return CandidateInvitation::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateInvitation::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateInvitation::DELETED_BY;
    }

    // functions
}
