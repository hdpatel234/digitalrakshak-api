<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function name()
    {
        return 'name';
    }

    public function group()
    {
        return 'group';
    }

    public function description()
    {
        return 'description';
    }

    public function guardName()
    {
        return 'guard_name';
    }
}
