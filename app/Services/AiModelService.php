<?php

namespace App\Services;

use App\Repositories\AiModelRepository;

/**
 * @property AiModelRepository $repository
 */
class AiModelService extends BaseService
{
    protected $repository;
    
    public function __construct(AiModelRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function modelName()
    {
        return $this->repository->modelName();
    }

    public function modelCode()
    {
        return $this->repository->modelCode();
    }

    public function modelType()
    {
        return $this->repository->modelType();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function maxTokens()
    {
        return $this->repository->maxTokens();
    }

    public function maxInputTokens()
    {
        return $this->repository->maxInputTokens();
    }

    public function maxOutputTokens()
    {
        return $this->repository->maxOutputTokens();
    }

    public function supportsFunctions()
    {
        return $this->repository->supportsFunctions();
    }

    public function supportsVision()
    {
        return $this->repository->supportsVision();
    }

    public function supportsStreaming()
    {
        return $this->repository->supportsStreaming();
    }

    public function supportsJsonMode()
    {
        return $this->repository->supportsJsonMode();
    }

    public function inputCostPer1k()
    {
        return $this->repository->inputCostPer1k();
    }

    public function outputCostPer1k()
    {
        return $this->repository->outputCostPer1k();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function capabilities()
    {
        return $this->repository->capabilities();
    }
    // functions
}