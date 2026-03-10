<?php

namespace App\Services;

use App\Repositories\TicketConversationRepository;

class TicketConversationService extends BaseService
{
    
    public function __construct(TicketConversationRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function ticketId()
    {
        return $this->repository->ticketId();
    }

    public function externalConversationId()
    {
        return $this->repository->externalConversationId();
    }

    public function message()
    {
        return $this->repository->message();
    }

    public function senderType()
    {
        return $this->repository->senderType();
    }

    public function senderName()
    {
        return $this->repository->senderName();
    }

    public function senderEmail()
    {
        return $this->repository->senderEmail();
    }

    public function isInternal()
    {
        return $this->repository->isInternal();
    }

    public function attachments()
    {
        return $this->repository->attachments();
    }

    public function conversationData()
    {
        return $this->repository->conversationData();
    }

    public function syncStatus()
    {
        return $this->repository->syncStatus();
    }
    // functions
}