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
        return $this->model::CANDIDATE_ID;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function packageId()
    {
        return $this->model::PACKAGE_ID;
    }

    public function invitationType()
    {
        return $this->model::INVITATION_TYPE;
    }

    public function invitationToken()
    {
        return $this->model::INVITATION_TOKEN;
    }

    public function formLink()
    {
        return $this->model::FORM_LINK;
    }

    public function formData()
    {
        return $this->model::FORM_DATA;
    }

    public function invitedBy()
    {
        return $this->model::INVITED_BY;
    }

    public function invitedAt()
    {
        return $this->model::INVITED_AT;
    }

    public function viewedAt()
    {
        return $this->model::VIEWED_AT;
    }

    public function reminderSentAt()
    {
        return $this->model::REMINDER_SENT_AT;
    }

    public function expiresAt()
    {
        return $this->model::EXPIRES_AT;
    }

    public function reminderCount()
    {
        return $this->model::REMINDER_COUNT;
    }

    public function lastReminderSentAt()
    {
        return $this->model::LAST_REMINDER_SENT_AT;
    }

    public function completedAt()
    {
        return $this->model::COMPLETED_AT;
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