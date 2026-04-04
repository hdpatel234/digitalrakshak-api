<?php

namespace App\Services\ApiService\Client;

use App\Enums\UserType;
use App\Services\ClientService;
use App\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;

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

    public function createUser() {}
    public function updateUser() {}
    public function destroyUser() {}
}
