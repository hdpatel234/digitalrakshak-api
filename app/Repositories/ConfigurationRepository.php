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
        return Configuration::CONFIG_KEY;
    }

    public function configValue()
    {
        return Configuration::CONFIG_VALUE;
    }

    public function description()
    {
        return Configuration::DESCRIPTION;
    }

    public function createdBy()
    {
        return Configuration::CREATED_BY;
    }

    public function updatedBy()
    {
        return Configuration::UPDATED_BY;
    }
}
