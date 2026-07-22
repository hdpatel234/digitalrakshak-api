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
        return EmailServerType::TYPE_NAME;
    }

    public function typeCode()
    {
        return EmailServerType::TYPE_CODE;
    }

    public function description()
    {
        return EmailServerType::DESCRIPTION;
    }

    public function isOutgoing()
    {
        return EmailServerType::IS_OUTGOING;
    }

    public function isIncoming()
    {
        return EmailServerType::IS_INCOMING;
    }

    // functions
}
