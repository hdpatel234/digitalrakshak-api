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
        return $this->model::USER_ID;
    }

    public function defaultDashboard()
    {
        return $this->model::DEFAULT_DASHBOARD;
    }

    public function widgetLayout()
    {
        return $this->model::WIDGET_LAYOUT;
    }

    public function hiddenWidgets()
    {
        return $this->model::HIDDEN_WIDGETS;
    }

    public function widgetSettings()
    {
        return $this->model::WIDGET_SETTINGS;
    }

    public function refreshInterval()
    {
        return $this->model::REFRESH_INTERVAL;
    }

    public function defaultView()
    {
        return $this->model::DEFAULT_VIEW;
    }

    public function itemsPerPage()
    {
        return $this->model::ITEMS_PER_PAGE;
    }
    // functions
}