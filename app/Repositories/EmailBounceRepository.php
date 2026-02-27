<?php

namespace App\Repositories;

use App\Models\EmailBounce;

class EmailBounceRepository extends BaseRepository
{
    public function __construct(EmailBounce $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function email()
    {
        return $this->model::EMAIL;
    }

    public function bounceType()
    {
        return $this->model::BOUNCE_TYPE;
    }

    public function reason()
    {
        return $this->model::REASON;
    }

    public function bouncedAt()
    {
        return $this->model::BOUNCED_AT;
    }

    public function unsubscribedAt()
    {
        return $this->model::UNSUBSCRIBED_AT;
    }

    public function blockedUntil()
    {
        return $this->model::BLOCKED_UNTIL;
    }

    public function bounceCount()
    {
        return $this->model::BOUNCE_COUNT;
    }
    // functions
}