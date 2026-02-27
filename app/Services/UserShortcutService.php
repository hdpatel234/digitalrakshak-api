<?php

namespace App\Services;

use App\Repositories\UserShortcutRepository;

class UserShortcutService extends BaseService
{
    protected $repository;
    
    public function __construct(UserShortcutRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function action()
    {
        return $this->repository->action();
    }

    public function shortcut()
    {
        return $this->repository->shortcut();
    }

    public function scope()
    {
        return $this->repository->scope();
    }

    public function isEnabled()
    {
        return $this->repository->isEnabled();
    }
    // functions
}