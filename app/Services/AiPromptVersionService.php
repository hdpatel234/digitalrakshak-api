<?php

namespace App\Services;

use App\Repositories\AiPromptVersionRepository;

class AiPromptVersionService extends BaseService
{
    protected $repository;
    
    public function __construct(AiPromptVersionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function promptId()
    {
        return $this->repository->promptId();
    }

    public function version()
    {
        return $this->repository->version();
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

    public function temperature()
    {
        return $this->repository->temperature();
    }

    public function maxTokens()
    {
        return $this->repository->maxTokens();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }
    // functions
}