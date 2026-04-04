<?php

namespace App\Http\Controllers\Api\Client\Members;

use App\Http\Controllers\Controller;
use App\Services\ApiService\Client\MemberService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $users = $this->memberService->createUser();
        return $this->success('Users fetched successfully', $users);
    }

    public function update(Request $request, $user)
    {
        $users = $this->memberService->updateUser();
        return $this->success('Users fetched successfully', $users);
    }

    public function destroy(Request $request, $user)
    {
        $users = $this->memberService->destroyUser();
        return $this->success('Users fetched successfully', $users);
    }
}
