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
        return ClientApiQuota::CLIENT_ID;
    }

    public function periodStart()
    {
        return ClientApiQuota::PERIOD_START;
    }

    public function periodEnd()
    {
        return ClientApiQuota::PERIOD_END;
    }

    public function requestsLimit()
    {
        return ClientApiQuota::REQUESTS_LIMIT;
    }

    public function requestsUsed()
    {
        return ClientApiQuota::REQUESTS_USED;
    }

    public function requestsRemaining()
    {
        return ClientApiQuota::REQUESTS_REMAINING;
    }

    public function resetAt()
    {
        return ClientApiQuota::RESET_AT;
    }
    // functions
}
