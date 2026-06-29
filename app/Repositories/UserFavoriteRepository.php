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
        return UserFavorite::USER_ID;
    }

    public function favoriteType()
    {
        return UserFavorite::FAVORITE_TYPE;
    }

    public function favoriteId()
    {
        return UserFavorite::FAVORITE_ID;
    }

    public function url()
    {
        return UserFavorite::URL;
    }

    public function title()
    {
        return UserFavorite::TITLE;
    }

    public function icon()
    {
        return UserFavorite::ICON;
    }

    public function metadata()
    {
        return UserFavorite::METADATA;
    }

    public function displayOrder()
    {
        return UserFavorite::DISPLAY_ORDER;
    }
    // functions
}