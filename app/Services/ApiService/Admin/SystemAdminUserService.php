<?php

namespace App\Services\ApiService\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemAdminUserService
{
    public function getAdminUsers(array $data)
    {
        $limit = $data['limit'] ?? 10;
        $search = $data['search'] ?? null;
        $role = $data['role'] ?? null;
        $status = $data['status'] ?? null;

        $query = User::with('roles')
            ->whereIn('user_type', ['super_admin', 'admin_user']);

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
            $query->where('status', strtolower($status));
        }

        $users = $query->paginate($limit);

        // Map users for frontend formatting
        $mappedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'role' => ucwords(str_replace('_', ' ', $user->roles->first() ? $user->roles->first()->name : $user->user_type)),
                'status' => ucfirst($user->status->value ?? $user->status),
                'lastLogin' => $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('Y-m-d H:i') : 'Never',
                'createdAt' => $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') : null,
            ];
        });

        // Set mapped items to paginator
        $users->setCollection($mappedUsers);

        $stats = [
            'total_admins' => User::whereIn('user_type', ['super_admin', 'admin_user'])->count(),
            'active_admins' => User::whereIn('user_type', ['super_admin', 'admin_user'])->where('status', 'active')->count(),
            'suspended_admins' => User::whereIn('user_type', ['super_admin', 'admin_user'])->where('status', 'suspended')->count(),
            'super_admins' => User::where('user_type', 'super_admin')->count(),
        ];

        return [
            'users' => $users,
            'stats' => $stats
        ];
    }

    public function showAdminUser(User $user)
    {
        if (!in_array($user->user_type->value ?? $user->user_type, ['super_admin', 'admin_user'])) {
            throw new \Exception('Not an admin user', 404);
        }

        return [
            'id' => $user->id,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'email' => $user->email,
            'role' => $user->roles->first() ? $user->roles->first()->name : '',
            'status' => $user->status->value ?? $user->status,
        ];
    }

    public function storeAdminUser(array $data)
    {
        $user = User::create([
            User::FIRST_NAME => $data['firstName'],
            User::LAST_NAME => $data['lastName'],
            User::EMAIL => $data['email'],
            User::USER_TYPE => 'admin_user',
            User::STATUS => 'active',
            User::PASSWORD => Hash::make(Str::random(12)), // Random password initially
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    public function updateAdminUser(User $user, array $data)
    {
        if (!in_array($user->user_type->value ?? $user->user_type, ['super_admin', 'admin_user'])) {
            throw new \Exception('Not an admin user', 404);
        }

        $user->update([
            User::FIRST_NAME => $data['firstName'],
            User::LAST_NAME => $data['lastName'],
            User::EMAIL => $data['email'],
            User::STATUS => $data['status'],
        ]);

        $user->syncRoles([$data['role']]);

        return $user;
    }

    public function updateAdminUserStatus(User $user, string $status)
    {
        if (!in_array($user->user_type->value ?? $user->user_type, ['super_admin', 'admin_user'])) {
            throw new \Exception('Not an admin user', 404);
        }

        $user->update([
            User::STATUS => $status,
        ]);

        return $user;
    }
}
