<?php

namespace App\Services;

use App\Repositories\AiPromptRepository;

class AiPromptService extends BaseService
{
    protected $repository;
    
    public function __construct(AiPromptRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function promptName()
    {
        return $this->repository->promptName();
    }

    public function promptCode()
    {
        return $this->repository->promptCode();
    }

    public function category()
    {
        return $this->repository->category();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function systemPrompt()
    {
        return $this->repository->systemPrompt();
    }

    public function userPromptTemplate()
    {
        return $this->repository->userPromptTemplate();
    }

    public function examples()
    {
        return $this->repository->examples();
    }

    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function modelId()
    {
        return $this->repository->modelId();
    }

    public function temperature()
    {
        return $this->repository->temperature();
    }

    public function maxTokens()
    {
        return $this->repository->maxTokens();
    }

    public function responseFormat()
    {
        return $this->repository->responseFormat();
    }

    public function responseSchema()
    {
        return $this->repository->responseSchema();
    }

    public function parseResponse()
    {
        return $this->repository->parseResponse();
    }

    public function functions()
    {
        return $this->repository->functions();
    }

    public function version()
    {
        return $this->repository->version();
    }

    public function isActive()
    {
        return $this->repository->isActive();
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