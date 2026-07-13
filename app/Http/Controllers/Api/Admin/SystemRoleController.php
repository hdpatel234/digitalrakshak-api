<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class SystemRoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');

        $query = Role::withCount('users')->where('is_admin_role', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type && $type !== 'all') {
            if ($type === 'system') {
                $query->where('is_system', true);
            } elseif ($type === 'custom') {
                $query->where('is_system', false);
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

        return response()->json([
            'status' => true,
            'message' => 'Roles fetched successfully',
            'data' => [
                'roles' => $roles
            ]
        ]);
    }

    public function show($id)
    {
        try {
            $role = Role::withCount('users')->where('is_admin_role', true)->findOrFail($id);
            
            $formattedRole = [
                'id' => $role->id,
                'name' => ucwords(str_replace('_', ' ', $role->name)),
                'description' => $role->description ?? 'No description provided',
                'type' => $role->is_system ? 'System' : 'Custom',
                'usersCount' => $role->users_count,
                'permissions' => $role->permissions->pluck('name'),
                'createdAt' => $role->created_at ? $role->created_at->format('Y-m-d H:i') : null,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Role fetched successfully',
                'data' => $formattedRole
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }
    }

    public function stats()
    {
        $stats = [
            'total_roles' => Role::where('is_admin_role', true)->count(),
            'system_roles' => Role::where('is_admin_role', true)->where('is_system', true)->count(),
            'custom_roles' => Role::where('is_admin_role', true)->where('is_system', false)->count(),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Role stats fetched successfully',
            'data' => $stats
        ]);
    }
    
    public function permissions()
    {
        $permissions = Permission::all()->groupBy('group')->map(function ($group, $key) {
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
        
        return response()->json([
            'status' => true,
            'message' => 'Permissions fetched successfully',
            'data' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => strtolower(str_replace(' ', '_', $request->name)),
                'description' => $request->description,
                'is_system' => false,
                'is_admin_role' => true,
                'guard_name' => 'api'
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Role created successfully',
                'data' => $role
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->is_system && $request->has('name') && strtolower(str_replace(' ', '_', $request->name)) !== $role->name) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot rename a system role'
            ], 403);
        }

        $request->validate([
            'name' => 'nullable|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            if (!$role->is_system && $request->has('name')) {
                $role->name = strtolower(str_replace(' ', '_', $request->name));
            }
            
            if ($request->has('description')) {
                $role->description = $request->description;
            }
            
            $role->save();

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete a system role'
            ], 403);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete this role because users are assigned to it.'
            ], 400);
        }

        try {
            $role->delete();
            return response()->json([
                'status' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }
}
