<?php

namespace App\Repositories;

use App\Models\EmailServerConfigurationValue;

class EmailServerConfigurationValueRepository extends BaseRepository
{
    public function __construct(EmailServerConfigurationValue $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function emailServerId()
    {
        return EmailServerConfigurationValue::EMAIL_SERVER_ID;
    }

    public function configurationFieldId()
    {
        return EmailServerConfigurationValue::CONFIGURATION_FIELD_ID;
    }

    public function fieldValue()
    {
        return EmailServerConfigurationValue::FIELD_VALUE;
    }
    // functions
}
