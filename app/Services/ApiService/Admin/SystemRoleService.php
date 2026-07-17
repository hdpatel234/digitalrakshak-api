<?php

namespace App\Services\ApiService\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;

class SystemRoleService
{
    public function __construct(
        protected RoleRepository $roleRepo,
        protected PermissionRepository $permissionRepo
    ) {}
    public function getRoles(array $data)
    {
        $search = $data['search'] ?? null;
        $type = $data['type'] ?? null;

        $query = $this->roleRepo->query()->withCount('users')->where($this->roleRepo->isAdminRole(), true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where($this->roleRepo->name(), 'like', "%{$search}%")
                  ->orWhere($this->roleRepo->description(), 'like', "%{$search}%");
            });
        }

        if ($type && $type !== 'all') {
            if ($type === 'system') {
                $query->where($this->roleRepo->isSystem(), true);
            } elseif ($type === 'custom') {
                $query->where($this->roleRepo->isSystem(), false);
            }
        }

        $roles = $query->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => ucwords(str_replace('_', ' ', $role->name)),
                'description' => $role->description ?? 'No description provided',
                'type' => $role->is_system ? 'System' : 'Custom',
                'usersCount' => $role->users_count,
                'permissions' => $role->permissions->pluck('name'),
                'createdAt' => $role->created_at ? $role->created_at->format('Y-m-d H:i') : null,
            ];
        });

        return [
            'roles' => $roles
        ];
    }

    public function showRole($id)
    {
        $role = $this->roleRepo->query()->withCount('users')->where($this->roleRepo->isAdminRole(), true)->findOrFail($id);
        
        return [
            'id' => $role->id,
            'name' => ucwords(str_replace('_', ' ', $role->name)),
            'description' => $role->description ?? 'No description provided',
            'type' => $role->is_system ? 'System' : 'Custom',
            'usersCount' => $role->users_count,
            'permissions' => $role->permissions->pluck('name'),
            'createdAt' => $role->created_at ? $role->created_at->format('Y-m-d H:i') : null,
        ];
    }

    public function getStats()
    {
        return [
            'total_roles' => $this->roleRepo->query()->where($this->roleRepo->isAdminRole(), true)->count(),
            'system_roles' => $this->roleRepo->query()->where($this->roleRepo->isAdminRole(), true)->where($this->roleRepo->isSystem(), true)->count(),
            'custom_roles' => $this->roleRepo->query()->where($this->roleRepo->isAdminRole(), true)->where($this->roleRepo->isSystem(), false)->count(),
        ];
    }
    
    public function getPermissions()
    {
        return $this->permissionRepo->all()->groupBy($this->permissionRepo->group())->map(function ($group, $key) {
            $groupName = $key ?: 'General';
            return [
                $groupName => $group->map(function ($perm) {
                    return [
                        'id' => $perm->name,
                        'name' => ucwords(str_replace('.', ' ', str_replace('_', ' ', $perm->name))),
                        'description' => $perm->description ?? 'Can access ' . $perm->name,
                    ];
                })
            ];
        })->collapse();
    }

    public function storeRole(array $data)
    {
        DB::beginTransaction();
        try {
            $role = $this->roleRepo->create([
                $this->roleRepo->name() => strtolower(str_replace(' ', '_', $data['name'])),
                $this->roleRepo->description() => $data['description'] ?? null,
                $this->roleRepo->isSystem() => false,
                $this->roleRepo->isAdminRole() => true,
                $this->roleRepo->guardName() => 'api'
            ]);

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create role: ' . $e->getMessage());
        }
    }

    public function updateRole($id, array $data)
    {
        $role = $this->roleRepo->find($id);
        
        if ($role->is_system && isset($data['name']) && strtolower(str_replace(' ', '_', $data['name'])) !== $role->name) {
            throw new \Exception('Cannot rename a system role', 403);
        }

        DB::beginTransaction();
        try {
            if (!$role->is_system && isset($data['name'])) {
                $role->name = strtolower(str_replace(' ', '_', $data['name']));
            }
            
            if (isset($data['description'])) {
                $role->description = $data['description'];
            }
            
            $role->save();

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to update role: ' . $e->getMessage());
        }
    }

    public function deleteRole($id)
    {
        $role = $this->roleRepo->find($id);

        if ($role->is_system) {
            throw new \Exception('Cannot delete a system role', 403);
        }

        if ($role->users()->count() > 0) {
            throw new \Exception('Cannot delete this role because users are assigned to it.', 400);
        }

        $role->delete();
        return true;
    }
}
