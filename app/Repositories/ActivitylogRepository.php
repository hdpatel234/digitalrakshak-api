<?php

namespace App\Repositories;

use App\Models\Activitylog;

class ActivitylogRepository extends BaseRepository
{
    public function __construct(Activitylog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function description()
    {
        return Activitylog::DESCRIPTION;
    }

    public function userId()
    {
        return Activitylog::USER_ID;
    }

    public function date()
    {
        return Activitylog::DATE;
    }

    public function ipAddress()
    {
        return Activitylog::IP_ADDRESS;
    }
    // functions
}