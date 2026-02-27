<?php

namespace App\Repositories;

use App\Models\UserSearchPreference;

class UserSearchPreferenceRepository extends BaseRepository
{
    public function __construct(UserSearchPreference $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function defaultSearchOperator()
    {
        return $this->model::DEFAULT_SEARCH_OPERATOR;
    }

    public function itemsPerPage()
    {
        return $this->model::ITEMS_PER_PAGE;
    }

    public function saveRecentSearches()
    {
        return $this->model::SAVE_RECENT_SEARCHES;
    }

    public function maxRecentSearches()
    {
        return $this->model::MAX_RECENT_SEARCHES;
    }

    public function saveFilters()
    {
        return $this->model::SAVE_FILTERS;
    }

    public function defaultDateRange()
    {
        return $this->model::DEFAULT_DATE_RANGE;
    }
    // functions
}