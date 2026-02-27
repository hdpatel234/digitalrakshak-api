<?php

namespace App\Repositories;

use App\Models\EmailServerType;

class EmailServerTypeRepository extends BaseRepository
{
    public function __construct(EmailServerType $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function typeName()
    {
        return $this->model::TYPE_NAME;
    }

    public function typeCode()
    {
        return $this->model::TYPE_CODE;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function isOutgoing()
    {
        return $this->model::IS_OUTGOING;
    }

    public function isIncoming()
    {
        return $this->model::IS_INCOMING;
    }

    public function configurationSchema()
    {
        return $this->model::CONFIGURATION_SCHEMA;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
}