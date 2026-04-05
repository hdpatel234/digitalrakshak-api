<?php

namespace App\Http\Controllers\Api\Client\Members;

use App\Http\Controllers\Controller;
use App\Services\ApiService\Client\MemberService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Client\Members\StoreMemberRequest;
use App\Http\Requests\Api\Client\Members\UpdateMemberRequest;

class MemberController extends Controller
{
    use ApiResponse;
    protected MemberService $memberService;
    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function index(Request $request)
    {
        addInfoLog("Users list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $users = $this->memberService->index($request->all(), $clientId);
            return $this->success('Users fetched successfully', $users);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $request, $user)
    {
        addInfoLog("Users show request");

        $mainUser = $request->user('api') ?? $request->user();
        $clientId = (int) ($mainUser?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $users = $this->memberService->showUser($user);
            return $this->success('Users fetched successfully', $users);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreMemberRequest $request)
    {
        addInfoLog("Users store request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $createdUser = $this->memberService->createUser($request->validated(), $clientId);
            return $this->success('User created successfully', $createdUser);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(UpdateMemberRequest $request, $user)
    {
        addInfoLog("Users update request");

        $mainUser = $request->user('api') ?? $request->user();
        $clientId = (int) ($mainUser?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $updatedUser = $this->memberService->updateUser($request->validated(), $clientId, $user);
            return $this->success('User updated successfully', $updatedUser);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function destroy(Request $request, $user)
    {
        addInfoLog("Users delete request");

        $mainUser = $request->user('api') ?? $request->user();
        $clientId = (int) ($mainUser?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $this->memberService->destroyUser($clientId, $user);
            return $this->success('User deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
