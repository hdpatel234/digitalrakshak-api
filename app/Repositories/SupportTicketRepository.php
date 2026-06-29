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
        return SupportTicket::CLIENT_ID;
    }

    public function supportConfigId()
    {
        return SupportTicket::SUPPORT_CONFIG_ID;
    }

    public function orderId()
    {
        return SupportTicket::ORDER_ID;
    }

    public function externalTicketId()
    {
        return SupportTicket::EXTERNAL_TICKET_ID;
    }

    public function ticketNumber()
    {
        return SupportTicket::TICKET_NUMBER;
    }

    public function subject()
    {
        return SupportTicket::SUBJECT;
    }

    public function description()
    {
        return SupportTicket::DESCRIPTION;
    }

    public function status()
    {
        return SupportTicket::STATUS;
    }

    public function departmentId()
    {
        return SupportTicket::DEPARTMENT_ID;
    }

    public function priorityId()
    {
        return SupportTicket::PRIORITY_ID;
    }

    public function assignedTo()
    {
        return SupportTicket::ASSIGNED_TO;
    }

    public function assignedName()
    {
        return SupportTicket::ASSIGNED_NAME;
    }

    public function resolution()
    {
        return SupportTicket::RESOLUTION;
    }

    public function resolvedAt()
    {
        return SupportTicket::RESOLVED_AT;
    }

    public function closedAt()
    {
        return SupportTicket::CLOSED_AT;
    }

    public function ticketData()
    {
        return SupportTicket::TICKET_DATA;
    }

    public function documentId()
    {
        return SupportTicket::DOCUMENT_ID;
    }

    public function syncStatus()
    {
        return SupportTicket::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return SupportTicket::SYNC_MESSAGE;
    }

    public function lastSyncAt()
    {
        return SupportTicket::LAST_SYNC_AT;
    }

    public function createdBy()
    {
        return SupportTicket::CREATED_BY;
    }

    public function updatedBy()
    {
        return SupportTicket::UPDATED_BY;
    }
    // functions
}
