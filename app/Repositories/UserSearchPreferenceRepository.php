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
        return UserSearchPreference::USER_ID;
    }

    public function defaultSearchOperator()
    {
        return UserSearchPreference::DEFAULT_SEARCH_OPERATOR;
    }

    public function itemsPerPage()
    {
        return UserSearchPreference::ITEMS_PER_PAGE;
    }

    public function saveRecentSearches()
    {
        return UserSearchPreference::SAVE_RECENT_SEARCHES;
    }

    public function maxRecentSearches()
    {
        return UserSearchPreference::MAX_RECENT_SEARCHES;
    }

    public function saveFilters()
    {
        return UserSearchPreference::SAVE_FILTERS;
    }

    public function defaultDateRange()
    {
        return UserSearchPreference::DEFAULT_DATE_RANGE;
    }
    // functions
}