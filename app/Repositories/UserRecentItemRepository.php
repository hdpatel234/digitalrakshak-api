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
        return UserRecentItem::USER_ID;
    }

    public function itemType()
    {
        return UserRecentItem::ITEM_TYPE;
    }

    public function itemId()
    {
        return UserRecentItem::ITEM_ID;
    }

    public function url()
    {
        return UserRecentItem::URL;
    }

    public function title()
    {
        return UserRecentItem::TITLE;
    }

    public function metadata()
    {
        return UserRecentItem::METADATA;
    }

    public function lastAccessedAt()
    {
        return UserRecentItem::LAST_ACCESSED_AT;
    }

    public function accessCount()
    {
        return UserRecentItem::ACCESS_COUNT;
    }
    // functions
}
