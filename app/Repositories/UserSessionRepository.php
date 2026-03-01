<?php

namespace App\Repositories;

use App\Models\UserSession;

class UserSessionRepository extends BaseRepository
{
    public function __construct(UserSession $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function accessTokenId()
    {
        return $this->model::ACCESS_TOKEN_ID;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }

    public function userAgent()
    {
        return $this->model::USER_AGENT;
    }

    public function browser()
    {
        return $this->model::BROWSER;
    }

    public function os()
    {
        return $this->model::OS;
    }

    public function device()
    {
        return $this->model::DEVICE;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
    public function markInactive($tokenId)
    {
        return $this->model::where($this->model::ACCESS_TOKEN_ID, $tokenId)->update([$this->model::IS_ACTIVE => false]);
    }
}