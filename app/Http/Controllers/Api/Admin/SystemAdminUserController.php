<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SystemAdminUserController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $search = $request->get('search');

        $query = User::with('roles')
            ->whereIn('user_type', ['super_admin', 'admin_user', 'admin']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($limit);

        // Map users for frontend formatting
        $mappedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'role' => $user->roles->first() ? $user->roles->first()->name : ($user->user_type === 'super_admin' ? 'Super Admin' : 'Admin'),
                'status' => $user->is_active ? 'Active' : 'Suspended',
                'lastLogin' => $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('Y-m-d H:i') : 'Never',
                'createdAt' => $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') : null,
            ];
        });

        // Set mapped items to paginator
        $users->setCollection($mappedUsers);

        $stats = [
            'total_admins' => User::whereIn('user_type', ['super_admin', 'admin_user', 'admin'])->count(),
            'active_admins' => User::whereIn('user_type', ['super_admin', 'admin_user', 'admin'])->where('is_active', 1)->count(),
            'suspended_admins' => User::whereIn('user_type', ['super_admin', 'admin_user', 'admin'])->where('is_active', 0)->count(),
            'super_admins' => User::where('user_type', 'super_admin')->count(),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Admin users fetched successfully',
            'data' => [
                'users' => $users,
                'stats' => $stats
            ]
        ]);
    }
}
