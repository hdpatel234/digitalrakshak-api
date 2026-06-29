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
        return EmailBounce::EMAIL;
    }

    public function bounceType()
    {
        return EmailBounce::BOUNCE_TYPE;
    }

    public function reason()
    {
        return EmailBounce::REASON;
    }

    public function bouncedAt()
    {
        return EmailBounce::BOUNCED_AT;
    }

    public function unsubscribedAt()
    {
        return EmailBounce::UNSUBSCRIBED_AT;
    }

    public function blockedUntil()
    {
        return EmailBounce::BLOCKED_UNTIL;
    }

    public function bounceCount()
    {
        return EmailBounce::BOUNCE_COUNT;
    }
    // functions
}