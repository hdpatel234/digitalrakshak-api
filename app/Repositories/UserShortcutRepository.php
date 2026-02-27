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
        return $this->model::USER_ID;
    }

    public function action()
    {
        return $this->model::ACTION;
    }

    public function shortcut()
    {
        return $this->model::SHORTCUT;
    }

    public function scope()
    {
        return $this->model::SCOPE;
    }

    public function isEnabled()
    {
        return $this->model::IS_ENABLED;
    }
    // functions
}