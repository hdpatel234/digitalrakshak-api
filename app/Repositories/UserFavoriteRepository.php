<?php

namespace App\Repositories;

use App\Models\UserFavorite;

class UserFavoriteRepository extends BaseRepository
{
    public function __construct(UserFavorite $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function favoriteType()
    {
        return $this->model::FAVORITE_TYPE;
    }

    public function favoriteId()
    {
        return $this->model::FAVORITE_ID;
    }

    public function url()
    {
        return $this->model::URL;
    }

    public function title()
    {
        return $this->model::TITLE;
    }

    public function icon()
    {
        return $this->model::ICON;
    }

    public function metadata()
    {
        return $this->model::METADATA;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }
    // functions
}