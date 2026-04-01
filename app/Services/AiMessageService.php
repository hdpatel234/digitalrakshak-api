<?php

namespace App\Services;

use App\Repositories\AiMessageRepository;

class AiMessageService extends BaseService
{
    protected $repository;
    
    public function __construct(AiMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function conversationId()
    {
        return $this->repository->conversationId();
    }

    public function messageUuid()
    {
        return $this->repository->messageUuid();
    }

    public function role()
    {
        return $this->repository->role();
    }

    public function content()
    {
        return $this->repository->content();
    }

    public function functionName()
    {
        return $this->repository->functionName();
    }

    public function functionArguments()
    {
        return $this->repository->functionArguments();
    }

    public function functionResponse()
    {
        return $this->repository->functionResponse();
    }

    public function modelId()
    {
        return $this->repository->modelId();
    }

    public function inputTokens()
    {
        return $this->repository->inputTokens();
    }

    public function outputTokens()
    {
        return $this->repository->outputTokens();
    }

    public function totalTokens()
    {
        return $this->repository->totalTokens();
    }

    public function cost()
    {
        return $this->repository->cost();
    }

    public function responseTimeMs()
    {
        return $this->repository->responseTimeMs();
    }

    public function finishReason()
    {
        return $this->repository->finishReason();
    }

    public function metadata()
    {
        return $this->repository->metadata();
    }

    public function rawRequest()
    {
        return $this->repository->rawRequest();
    }

    public function rawResponse()
    {
        return $this->repository->rawResponse();
    }

    public function userRating()
    {
        return $this->repository->userRating();
    }

    public function userFeedback()
    {
        return $this->repository->userFeedback();
    }
    // functions
}