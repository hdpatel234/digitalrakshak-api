<?php

namespace App\Services;

use App\Repositories\SupportTicketRepository;

/**
 * @property SupportTicketRepository $repository
 */
class SupportTicketService extends BaseService
{

    public function __construct(SupportTicketRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function supportConfigId()
    {
        return $this->repository->supportConfigId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function externalTicketId()
    {
        return $this->repository->externalTicketId();
    }

    public function ticketNumber()
    {
        return $this->repository->ticketNumber();
    }

    public function subject()
    {
        return $this->repository->subject();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function departmentId()
    {
        return $this->repository->departmentId();
    }

    public function priorityId()
    {
        return $this->repository->priorityId();
    }

    public function assignedTo()
    {
        return $this->repository->assignedTo();
    }

    public function assignedName()
    {
        return $this->repository->assignedName();
    }

    public function resolution()
    {
        return $this->repository->resolution();
    }

    public function resolvedAt()
    {
        return $this->repository->resolvedAt();
    }

    public function closedAt()
    {
        return $this->repository->closedAt();
    }

    public function ticketData()
    {
        return $this->repository->ticketData();
    }

    public function documentId()
    {
        return $this->repository->documentId();
    }

    public function syncStatus()
    {
        return $this->repository->syncStatus();
    }

    public function syncMessage()
    {
        return $this->repository->syncMessage();
    }

    public function lastSyncAt()
    {
        return $this->repository->lastSyncAt();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}
