<?php

namespace App\Repositories;

use App\Models\UserDashboardPreference;

class UserDashboardPreferenceRepository extends BaseRepository
{
    public function __construct(UserDashboardPreference $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserDashboardPreference::USER_ID;
    }

    public function defaultDashboard()
    {
        return UserDashboardPreference::DEFAULT_DASHBOARD;
    }

    public function widgetLayout()
    {
        return UserDashboardPreference::WIDGET_LAYOUT;
    }

    public function hiddenWidgets()
    {
        return UserDashboardPreference::HIDDEN_WIDGETS;
    }

    public function widgetSettings()
    {
        return UserDashboardPreference::WIDGET_SETTINGS;
    }

    public function refreshInterval()
    {
        return UserDashboardPreference::REFRESH_INTERVAL;
    }

    public function defaultView()
    {
        return UserDashboardPreference::DEFAULT_VIEW;
    }

    public function itemsPerPage()
    {
        return UserDashboardPreference::ITEMS_PER_PAGE;
    }
    // functions
}