<?php

namespace App\Repositories;

use App\Models\UserConfigValue;

class UserConfigValueRepository extends BaseRepository
{
    public function __construct(UserConfigValue $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function configId()
    {
        return $this->model::CONFIG_ID;
    }

    public function value()
    {
        return $this->model::VALUE;
    }
    // functions
}