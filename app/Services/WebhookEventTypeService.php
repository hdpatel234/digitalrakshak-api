<?php

namespace App\Services;

use App\Repositories\WebhookEventTypeRepository;

/**
 * @property WebhookEventTypeRepository $repository
 */
class WebhookEventTypeService extends BaseService
{
    
    public function __construct(WebhookEventTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function eventName()
    {
        return $this->repository->eventName();
    }

    public function eventCode()
    {
        return $this->repository->eventCode();
    }

    public function category()
    {
        return $this->repository->category();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function samplePayload()
    {
        return $this->repository->samplePayload();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}
