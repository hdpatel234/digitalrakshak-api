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
        $role = $request->get('role');
        $status = $request->get('status');

        $query = User::with('roles')
            ->whereIn('user_type', ['super_admin', 'admin_user', 'admin']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'all') {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('id', $role);
            });
        }

        if ($status && $status !== 'all') {
            if (in_array(strtolower($status), ['active'])) {
                $query->where('is_active', 1);
            } elseif (in_array(strtolower($status), ['inactive', 'suspended'])) {
                $query->where('is_active', 0);
            }
        }

        $users = $query->paginate($limit);

        // Map users for frontend formatting
        $mappedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'role' => ucwords(str_replace('_', ' ', $user->roles->first() ? $user->roles->first()->name : $user->user_type)),
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            User::FIRST_NAME => $validated['firstName'],
            User::LAST_NAME => $validated['lastName'],
            User::EMAIL => $validated['email'],
            User::USER_TYPE => 'admin',
            User::IS_ACTIVE => 1,
            User::IS_ADMIN => 1,
            User::PASSWORD => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)), // Random password initially
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'status' => true,
            'message' => 'Admin user created successfully',
            'data' => $user
        ]);
    }
}
