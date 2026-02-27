<?php

namespace App\Services;

use App\Repositories\UserFavoriteRepository;

class UserFavoriteService extends BaseService
{
    protected $repository;
    
    public function __construct(UserFavoriteRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function favoriteType()
    {
        return $this->repository->favoriteType();
    }

    public function favoriteId()
    {
        return $this->repository->favoriteId();
    }

    public function url()
    {
        return $this->repository->url();
    }

    public function title()
    {
        return $this->repository->title();
    }

    public function icon()
    {
        return $this->repository->icon();
    }

    public function metadata()
    {
        return $this->repository->metadata();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }
    // functions
}