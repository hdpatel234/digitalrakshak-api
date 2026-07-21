<?php

namespace App\Services;

use App\Repositories\UserDashboardPreferenceRepository;

/**
 * @property UserDashboardPreferenceRepository $repository
 */
class UserDashboardPreferenceService extends BaseService
{
    
    public function __construct(UserDashboardPreferenceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function defaultDashboard()
    {
        return $this->repository->defaultDashboard();
    }

    public function widgetLayout()
    {
        return $this->repository->widgetLayout();
    }

    public function hiddenWidgets()
    {
        return $this->repository->hiddenWidgets();
    }

    public function widgetSettings()
    {
        return $this->repository->widgetSettings();
    }

    public function refreshInterval()
    {
        return $this->repository->refreshInterval();
    }

    public function defaultView()
    {
        return $this->repository->defaultView();
    }

    public function itemsPerPage()
    {
        return $this->repository->itemsPerPage();
    }
    // functions
}
