<?php

namespace App\Services;

use App\Repositories\UserRecentItemRepository;

/**
 * @property UserRecentItemRepository $repository
 */
class UserRecentItemService extends BaseService
{
    
    public function __construct(UserRecentItemRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function itemType()
    {
        return $this->repository->itemType();
    }

    public function itemId()
    {
        return $this->repository->itemId();
    }

    public function url()
    {
        return $this->repository->url();
    }

    public function title()
    {
        return $this->repository->title();
    }

    public function metadata()
    {
        return $this->repository->metadata();
    }

    public function lastAccessedAt()
    {
        return $this->repository->lastAccessedAt();
    }

    public function accessCount()
    {
        return $this->repository->accessCount();
    }
    // functions
}
