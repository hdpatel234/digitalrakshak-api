<?php

namespace App\Repositories;

use App\Models\UserDatetimePreference;

class UserDatetimePreferenceRepository extends BaseRepository
{
    public function __construct(UserDatetimePreference $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function timezone()
    {
        return $this->model::TIMEZONE;
    }

    public function dateFormat()
    {
        return $this->model::DATE_FORMAT;
    }

    public function timeFormat()
    {
        return $this->model::TIME_FORMAT;
    }

    public function firstDayOfWeek()
    {
        return $this->model::FIRST_DAY_OF_WEEK;
    }

    public function weekStartsOn()
    {
        return $this->model::WEEK_STARTS_ON;
    }

    public function showWeekNumbers()
    {
        return $this->model::SHOW_WEEK_NUMBERS;
    }

    public function calendarView()
    {
        return $this->model::CALENDAR_VIEW;
    }

    public function workingHoursStart()
    {
        return $this->model::WORKING_HOURS_START;
    }

    public function workingHoursEnd()
    {
        return $this->model::WORKING_HOURS_END;
    }

    public function workingDays()
    {
        return $this->model::WORKING_DAYS;
    }
    // functions
}