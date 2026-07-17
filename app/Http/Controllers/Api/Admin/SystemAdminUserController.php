<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Http\Requests\Api\Admin\StoreSystemAdminUserRequest;
use App\Http\Requests\Api\Admin\UpdateSystemAdminUserRequest;
use App\Http\Requests\Api\Admin\UpdateSystemAdminUserStatusRequest;
use App\Services\ApiService\Admin\SystemAdminUserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SystemAdminUserController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected SystemAdminUserService $systemAdminUserService
    ) {}

    public function index(Request $request)
    {
        addInfoLog("Admin system admin user list request");

        $data = $this->systemAdminUserService->getAdminUsers($request->all());

        return $this->success('Admin users fetched successfully', $data);
    }

    public function store(StoreSystemAdminUserRequest $request)
    {
        addInfoLog("Admin system admin user create request");

        $user = $this->systemAdminUserService->storeAdminUser($request->validated());

        return $this->success('Admin user created successfully', $user);
    }

    public function show(User $user)
    {
        addInfoLog("Admin system admin user show request");

        try {
            $data = $this->systemAdminUserService->showAdminUser($user);
            return $this->success('Admin user retrieved successfully', $data);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage(), $code);
        }
    }

    public function update(UpdateSystemAdminUserRequest $request, User $user)
    {
        addInfoLog("Admin system admin user update request");

        try {
            $updatedUser = $this->systemAdminUserService->updateAdminUser($user, $request->validated());
            return $this->success('Admin user updated successfully', $updatedUser);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage(), $code);
        }
    }

    public function updateStatus(UpdateSystemAdminUserStatusRequest $request, User $user)
    {
        addInfoLog("Admin system admin user update status request");

        try {
            $updatedUser = $this->systemAdminUserService->updateAdminUserStatus($user, $request->validated()['status']);
            return $this->success('Status updated successfully', $updatedUser);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage(), $code);
        }
    }
}
