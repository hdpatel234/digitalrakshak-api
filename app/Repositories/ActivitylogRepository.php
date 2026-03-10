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
        return $this->model::DESCRIPTION;
    }

    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function date()
    {
        return $this->model::DATE;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }
    // functions
}