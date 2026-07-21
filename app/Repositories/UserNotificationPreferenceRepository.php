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
        return UserNotificationPreference::USER_ID;
    }

    public function notificationType()
    {
        return UserNotificationPreference::NOTIFICATION_TYPE;
    }

    public function eventType()
    {
        return UserNotificationPreference::EVENT_TYPE;
    }

    public function enabled()
    {
        return UserNotificationPreference::ENABLED;
    }

    public function channels()
    {
        return UserNotificationPreference::CHANNELS;
    }

    public function quietHoursStart()
    {
        return UserNotificationPreference::QUIET_HOURS_START;
    }

    public function quietHoursEnd()
    {
        return UserNotificationPreference::QUIET_HOURS_END;
    }

    public function digestFrequency()
    {
        return UserNotificationPreference::DIGEST_FREQUENCY;
    }
    // functions
}
