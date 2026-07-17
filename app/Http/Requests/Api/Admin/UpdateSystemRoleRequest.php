<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\BaseRequest;
use Spatie\Permission\Models\Role;

class UpdateSystemRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        $roleId = $this->route('id') ?? ($this->route('role') ? $this->route('role')->id : null);
        
        return [
            'name' => 'nullable|string|max:255|unique:roles,name,' . $roleId,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }
}
