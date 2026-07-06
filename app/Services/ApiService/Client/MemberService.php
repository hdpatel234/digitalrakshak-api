<?php

namespace App\Services\ApiService\Client;

use App\Enums\UserType;
use App\Models\User;
use App\Services\ClientService;
use App\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberService extends BaseService
{
    protected UserRepository $userRepository;
    protected ClientService $clientService;

    public function __construct(UserRepository $userRepository, ClientService $clientService)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
        $this->clientService = $clientService;
    }

    public function index(array $params, int $clientId): array
    {
        $userTable = $this->userRepository->query()->getModel()->getTable();
        $firstNameColumn = $this->userRepository->firstName();
        $lastNameColumn = $this->userRepository->lastName();
        $emailColumn = $this->userRepository->email();
        $phoneColumn = $this->userRepository->phone();
        $isActiveColumn = $this->userRepository->isActive();
        $createdAtColumn = $this->userRepository->createdAt();

        $query = $this->query()->where($this->userRepository->clientID(), $clientId)->where($this->userRepository->userType(), '=', UserType::CLIENT_USER);

        return $this->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $userTable . '.' . $firstNameColumn,
                    $userTable . '.' . $lastNameColumn,
                    $userTable . '.' . $emailColumn,
                    $userTable . '.' . $phoneColumn,
                ],
                'status_column' => $userTable . '.' . $isActiveColumn,
                'date_column' => $userTable . '.' . $createdAtColumn,
                'allowed_sorts' => [
                    $userTable . '.' . $this->userRepository->id(),
                    $userTable . '.' . $firstNameColumn,
                    $userTable . '.' . $lastNameColumn,
                    $userTable . '.' . $emailColumn,
                    $userTable . '.' . $createdAtColumn,
                ],
                'default_sort_by' => $userTable . '.' . $createdAtColumn,
                'default_sort_direction' => 'desc',
            ]
        );
    }

    public function createUser(array $data, int $clientId)
    {
        $permissions = [];
        $hasPermissions = false;
        if (array_key_exists('permissions', $data)) {
            $permissions = $data['permissions'] ?? [];
            $hasPermissions = true;
            unset($data['permissions']);
        }

        $data[$this->userRepository->clientID()] = $clientId;
        $data[$this->userRepository->userType()] = UserType::CLIENT_USER;
        $data[$this->userRepository->isActive()] = 1;

        if (!isset($data[$this->userRepository->password()])) {
            $data[$this->userRepository->password()] = Hash::make(Str::random(12));
        }

        $user = $this->userRepository->create($data);
        
        // Ensure user has client_user role
        if (!$user->hasRole('client_user')) {
            $user->assignRole('client_user');
        }

        if ($hasPermissions) {
            $user->syncPermissions($permissions);
        }

        return $user;
    }
    public function showUser($user)
    {
        return $this->userRepository->query()->with('permissions')->find($user);
    }
    public function updateUser(array $data, int $clientId, $userId)
    {
        $userObj = $this->query()->where($this->userRepository->clientID(), $clientId)
            ->where($this->userRepository->id(), $userId)
            ->where($this->userRepository->userType(), '=', UserType::CLIENT_USER)
            ->first();

        if (!$userObj) {
            throw new \Exception("User not found or you don't have permission to update this user.", 404);
        }

        $permissions = [];
        $hasPermissions = false;
        if (array_key_exists('permissions', $data)) {
            $permissions = $data['permissions'] ?? [];
            $hasPermissions = true;
            unset($data['permissions']);
        }

        $user = $this->userRepository->update($userId, $data);

        if ($hasPermissions) {
            $userObj->syncPermissions($permissions);
        }

        return $user;
    }
    public function destroyUser(int $clientId, $userId)
    {
        $userObj = $this->query()->where($this->userRepository->clientID(), $clientId)
            ->where($this->userRepository->id(), $userId)
            ->where($this->userRepository->userType(), '=', UserType::CLIENT_USER)
            ->first();

        if (!$userObj) {
            throw new \Exception("User not found or you don't have permission to delete this user.", 404);
        }

        return $this->userRepository->delete($userId);
    }
}
