<?php

namespace App\Repositories;

use App\Models\SupportTicket;

class SupportTicketRepository extends BaseRepository
{
    public function __construct(SupportTicket $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function supportConfigId()
    {
        return $this->model::SUPPORT_CONFIG_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function externalTicketId()
    {
        return $this->model::EXTERNAL_TICKET_ID;
    }

    public function ticketNumber()
    {
        return $this->model::TICKET_NUMBER;
    }

    public function subject()
    {
        return $this->model::SUBJECT;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function departmentId()
    {
        return $this->model::DEPARTMENT_ID;
    }

    public function priorityId()
    {
        return $this->model::PRIORITY_ID;
    }

    public function assignedTo()
    {
        return $this->model::ASSIGNED_TO;
    }

    public function assignedName()
    {
        return $this->model::ASSIGNED_NAME;
    }

    public function resolution()
    {
        return $this->model::RESOLUTION;
    }

    public function resolvedAt()
    {
        return $this->model::RESOLVED_AT;
    }

    public function closedAt()
    {
        return $this->model::CLOSED_AT;
    }

    public function ticketData()
    {
        return $this->model::TICKET_DATA;
    }

    public function documentId()
    {
        return $this->model::DOCUMENT_ID;
    }

    public function syncStatus()
    {
        return $this->model::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return $this->model::SYNC_MESSAGE;
    }

    public function lastSyncAt()
    {
        return $this->model::LAST_SYNC_AT;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}
