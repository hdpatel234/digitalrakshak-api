<?php

namespace App\Repositories;

use App\Models\UserSavedSearch;

class UserSavedSearchRepository extends BaseRepository
{
    public function __construct(UserSavedSearch $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserSavedSearch::USER_ID;
    }

    public function searchName()
    {
        return UserSavedSearch::SEARCH_NAME;
    }

    public function entityType()
    {
        return UserSavedSearch::ENTITY_TYPE;
    }

    public function filters()
    {
        return UserSavedSearch::FILTERS;
    }

    public function columns()
    {
        return UserSavedSearch::COLUMNS;
    }

    public function sort()
    {
        return UserSavedSearch::SORT;
    }

    public function isShared()
    {
        return UserSavedSearch::IS_SHARED;
    }
    // functions
}
