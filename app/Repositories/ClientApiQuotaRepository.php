<?php

namespace App\Repositories;

use App\Models\ClientApiQuota;

class ClientApiQuotaRepository extends BaseRepository
{
    public function __construct(ClientApiQuota $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function periodStart()
    {
        return $this->model::PERIOD_START;
    }

    public function periodEnd()
    {
        return $this->model::PERIOD_END;
    }

    public function requestsLimit()
    {
        return $this->model::REQUESTS_LIMIT;
    }

    public function requestsUsed()
    {
        return $this->model::REQUESTS_USED;
    }

    public function requestsRemaining()
    {
        return $this->model::REQUESTS_REMAINING;
    }

    public function resetAt()
    {
        return $this->model::RESET_AT;
    }
    // functions
}