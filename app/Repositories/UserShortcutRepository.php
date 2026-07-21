<?php

namespace App\Repositories;

use App\Models\UserShortcut;

class UserShortcutRepository extends BaseRepository
{
    public function __construct(UserShortcut $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserShortcut::USER_ID;
    }

    public function action()
    {
        return UserShortcut::ACTION;
    }

    public function shortcut()
    {
        return UserShortcut::SHORTCUT;
    }

    public function scope()
    {
        return UserShortcut::SCOPE;
    }

    public function isEnabled()
    {
        return UserShortcut::IS_ENABLED;
    }
    // functions
}
