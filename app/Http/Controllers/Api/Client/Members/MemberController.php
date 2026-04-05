<?php

namespace App\Http\Controllers\Api\Client\Members;

use App\Http\Controllers\Controller;
use App\Services\ApiService\Client\MemberService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Client\Members\StoreMemberRequest;

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

    public function update(Request $request, $user)
    {
        addInfoLog("Users update request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $users = $this->memberService->updateUser($request->all(), $clientId);
            return $this->success('Users fetched successfully', $users);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function destroy(Request $request, $user)
    {
        addInfoLog("Users delete request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $users = $this->memberService->destroyUser($request->all(), $clientId);
            return $this->success('Users fetched successfully', $users);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
