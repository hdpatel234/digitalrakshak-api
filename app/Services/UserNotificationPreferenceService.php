<?php

namespace App\Services;

use App\Repositories\UserNotificationPreferenceRepository;

/**
 * @property UserNotificationPreferenceRepository $repository
 */
class UserNotificationPreferenceService extends BaseService
{
    
    public function __construct(UserNotificationPreferenceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function notificationType()
    {
        return $this->repository->notificationType();
    }

    public function eventType()
    {
        return $this->repository->eventType();
    }

    public function enabled()
    {
        return $this->repository->enabled();
    }

    public function channels()
    {
        return $this->repository->channels();
    }

    public function quietHoursStart()
    {
        return $this->repository->quietHoursStart();
    }

    public function quietHoursEnd()
    {
        return $this->repository->quietHoursEnd();
    }

    public function digestFrequency()
    {
        return $this->repository->digestFrequency();
    }
    // functions
}
