<?php

namespace App\Services;

use App\Repositories\AiConversationRepository;

/**
 * @property AiConversationRepository $repository
 */
class AiConversationService extends BaseService
{
    protected $repository;
    
    public function __construct(AiConversationRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function conversationUuid()
    {
        return $this->repository->conversationUuid();
    }

    public function userId()
    {
        return $this->repository->userId();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function configId()
    {
        return $this->repository->configId();
    }

    public function modelId()
    {
        return $this->repository->modelId();
    }

    public function promptId()
    {
        return $this->repository->promptId();
    }

    public function title()
    {
        return $this->repository->title();
    }

    public function context()
    {
        return $this->repository->context();
    }

    public function totalTokens()
    {
        return $this->repository->totalTokens();
    }

    public function totalCost()
    {
        return $this->repository->totalCost();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function lastMessageAt()
    {
        return $this->repository->lastMessageAt();
    }
    // functions
}