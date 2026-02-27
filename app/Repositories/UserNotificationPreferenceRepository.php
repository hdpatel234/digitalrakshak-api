<?php

namespace App\Repositories;

use App\Models\UserNotificationPreference;

class UserNotificationPreferenceRepository extends BaseRepository
{
    public function __construct(UserNotificationPreference $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function notificationType()
    {
        return $this->model::NOTIFICATION_TYPE;
    }

    public function eventType()
    {
        return $this->model::EVENT_TYPE;
    }

    public function enabled()
    {
        return $this->model::ENABLED;
    }

    public function channels()
    {
        return $this->model::CHANNELS;
    }

    public function quietHoursStart()
    {
        return $this->model::QUIET_HOURS_START;
    }

    public function quietHoursEnd()
    {
        return $this->model::QUIET_HOURS_END;
    }

    public function digestFrequency()
    {
        return $this->model::DIGEST_FREQUENCY;
    }
    // functions
}