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
        return $this->model::USER_ID;
    }

    public function searchName()
    {
        return $this->model::SEARCH_NAME;
    }

    public function entityType()
    {
        return $this->model::ENTITY_TYPE;
    }

    public function filters()
    {
        return $this->model::FILTERS;
    }

    public function columns()
    {
        return $this->model::COLUMNS;
    }

    public function sort()
    {
        return $this->model::SORT;
    }

    public function isShared()
    {
        return $this->model::IS_SHARED;
    }
    // functions
}