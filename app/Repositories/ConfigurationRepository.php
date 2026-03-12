<?php

namespace App\Repositories;

use App\Models\Configuration;

class ConfigurationRepository extends BaseRepository
{
    public function __construct(Configuration $model)
    {
        parent::__construct($model);
    }

    public function configKey()
    {
        return $this->model::CONFIG_KEY;
    }

    public function configValue()
    {
        return $this->model::CONFIG_VALUE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
}
