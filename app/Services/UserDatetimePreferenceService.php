<?php

namespace App\Services;

use App\Repositories\UserDatetimePreferenceRepository;

/**
 * @property UserDatetimePreferenceRepository $repository
 */
class UserDatetimePreferenceService extends BaseService
{
    
    public function __construct(UserDatetimePreferenceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function timezone()
    {
        return $this->repository->timezone();
    }

    public function dateFormat()
    {
        return $this->repository->dateFormat();
    }

    public function timeFormat()
    {
        return $this->repository->timeFormat();
    }

    public function firstDayOfWeek()
    {
        return $this->repository->firstDayOfWeek();
    }

    public function weekStartsOn()
    {
        return $this->repository->weekStartsOn();
    }

    public function showWeekNumbers()
    {
        return $this->repository->showWeekNumbers();
    }

    public function calendarView()
    {
        return $this->repository->calendarView();
    }

    public function workingHoursStart()
    {
        return $this->repository->workingHoursStart();
    }

    public function workingHoursEnd()
    {
        return $this->repository->workingHoursEnd();
    }

    public function workingDays()
    {
        return $this->repository->workingDays();
    }
    // functions
}
