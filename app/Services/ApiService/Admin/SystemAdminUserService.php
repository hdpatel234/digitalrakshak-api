<?php

namespace App\Services\ApiService\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Repositories\UserRepository;

class SystemAdminUserService
{
    public function __construct(
        protected UserRepository $repo
    ) {}
    public function getAdminUsers(array $data)
    {
        $limit = $data['limit'] ?? 10;
        $search = $data['search'] ?? null;
        $role = $data['role'] ?? null;
        $status = $data['status'] ?? null;

        $query = $this->repo->query()->with('roles')
            ->whereIn($this->repo->userType(), ['super_admin', 'admin_user']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where($this->repo->firstName(), 'like', "%{$search}%")
                  ->orWhere($this->repo->lastName(), 'like', "%{$search}%")
                  ->orWhere($this->repo->email(), 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'all') {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('id', $role);
            });
        }

        if ($status && $status !== 'all') {
            $query->where($this->repo->status(), strtolower($status));
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
            'total_admins' => $this->repo->query()->whereIn($this->repo->userType(), ['super_admin', 'admin_user'])->count(),
            'active_admins' => $this->repo->query()->whereIn($this->repo->userType(), ['super_admin', 'admin_user'])->where($this->repo->status(), 'active')->count(),
            'suspended_admins' => $this->repo->query()->whereIn($this->repo->userType(), ['super_admin', 'admin_user'])->where($this->repo->status(), 'suspended')->count(),
            'super_admins' => $this->repo->query()->where($this->repo->userType(), 'super_admin')->count(),
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
        $user = $this->repo->create([
            $this->repo->firstName() => $data['firstName'],
            $this->repo->lastName() => $data['lastName'],
            $this->repo->email() => $data['email'],
            $this->repo->userType() => 'admin_user',
            $this->repo->status() => 'active',
            $this->repo->password() => Hash::make(Str::random(12)), // Random password initially
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    public function updateAdminUser(User $user, array $data)
    {
        if (!in_array($user->user_type->value ?? $user->user_type, ['super_admin', 'admin_user'])) {
            throw new \Exception('Not an admin user', 404);
        }

        $this->repo->update($user->{$this->repo->id()}, [
            $this->repo->firstName() => $data['firstName'],
            $this->repo->lastName() => $data['lastName'],
            $this->repo->email() => $data['email'],
            $this->repo->status() => $data['status'],
        ]);

        $user->syncRoles([$data['role']]);

        return $user;
    }

    public function updateAdminUserStatus(User $user, string $status)
    {
        if (!in_array($user->user_type->value ?? $user->user_type, ['super_admin', 'admin_user'])) {
            throw new \Exception('Not an admin user', 404);
        }

        $this->repo->update($user->{$this->repo->id()}, [
            $this->repo->status() => $status,
        ]);

        return $user;
    }
}
