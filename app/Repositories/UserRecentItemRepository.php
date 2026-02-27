<?php

namespace App\Repositories;

use App\Models\UserRecentItem;

class UserRecentItemRepository extends BaseRepository
{
    public function __construct(UserRecentItem $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function itemType()
    {
        return $this->model::ITEM_TYPE;
    }

    public function itemId()
    {
        return $this->model::ITEM_ID;
    }

    public function url()
    {
        return $this->model::URL;
    }

    public function title()
    {
        return $this->model::TITLE;
    }

    public function metadata()
    {
        return $this->model::METADATA;
    }

    public function lastAccessedAt()
    {
        return $this->model::LAST_ACCESSED_AT;
    }

    public function accessCount()
    {
        return $this->model::ACCESS_COUNT;
    }
    // functions
}