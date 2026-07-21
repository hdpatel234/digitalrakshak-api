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
        return UserDatetimePreference::USER_ID;
    }

    public function timezone()
    {
        return UserDatetimePreference::TIMEZONE;
    }

    public function dateFormat()
    {
        return UserDatetimePreference::DATE_FORMAT;
    }

    public function timeFormat()
    {
        return UserDatetimePreference::TIME_FORMAT;
    }

    public function firstDayOfWeek()
    {
        return UserDatetimePreference::FIRST_DAY_OF_WEEK;
    }

    public function weekStartsOn()
    {
        return UserDatetimePreference::WEEK_STARTS_ON;
    }

    public function showWeekNumbers()
    {
        return UserDatetimePreference::SHOW_WEEK_NUMBERS;
    }

    public function calendarView()
    {
        return UserDatetimePreference::CALENDAR_VIEW;
    }

    public function workingHoursStart()
    {
        return UserDatetimePreference::WORKING_HOURS_START;
    }

    public function workingHoursEnd()
    {
        return UserDatetimePreference::WORKING_HOURS_END;
    }

    public function workingDays()
    {
        return UserDatetimePreference::WORKING_DAYS;
    }
    // functions
}
