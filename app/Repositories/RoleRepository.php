<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function name()
    {
        return 'name';
    }

    public function description()
    {
        return 'description';
    }

    public function isSystem()
    {
        return 'is_system';
    }

    public function isAdminRole()
    {
        return 'is_admin_role';
    }

    public function guardName()
    {
        return 'guard_name';
    }
}
