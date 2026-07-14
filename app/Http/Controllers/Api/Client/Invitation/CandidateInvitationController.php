<?php

namespace App\Http\Controllers\Api\Client\Invitation;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Invitation\ParseResumeRequest;
use App\Http\Requests\Api\Client\Invitation\StoreCandidateInvitationRequest;
use App\Http\Requests\Api\Client\Invitation\UpdateCandidateInvitationByTokenRequest;
use App\Services\ApiService\Client\CandidateInvitationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CandidateInvitationController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected CandidateInvitationService $candidateInvitationService
    ) {}

    public function index(Request $request)
    {
        addInfoLog("Candidates Invitation list index");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $result = $this->candidateInvitationService->getInvitations($request->all(), $clientId);

        return $this->success('Invitations fetched successfully.', $result);
    }

    public function store(StoreCandidateInvitationRequest $request)
    {
        addInfoLog("Candiate Invitation create request");

        return $this->handleInvitationCreation($request, $request->validated());
    }

    public function invite(StoreCandidateInvitationRequest $request, int $candidate)
    {
        addInfoLog("Candiate Invitation send request");

        $payload = $request->validated();
        $payload['candidate_ids'] = [$candidate];

        return $this->handleInvitationCreation($request, $payload);
    }

    protected function handleInvitationCreation(Request $request, array $payload)
    {
        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->candidateInvitationService->createInvitations(
                $payload,
                $clientId,
                $user,
                (string) $request->ip(),
                (string) $request->userAgent()
            );

            return $this->success('Candidate invitations created successfully.', $result, 201);
        } catch (\Exception $e) {
            Log::error('Invitation creation failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function showByToken(Request $request, $token)
    {
        addInfoLog("Candiate Invitation show by token request");

        try {
            $invitationData = $this->candidateInvitationService->getInvitationByToken((string) $token);

            return $this->success('Invitation fetched successfully.', $invitationData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateByToken(UpdateCandidateInvitationByTokenRequest $request, $token)
    {
        addInfoLog("Candidate invitation data update by token");

        try {
            $this->candidateInvitationService->updateInvitationByToken(
                (string) $token,
                $request->validated(),
                (string) $request->ip(),
                (string) $request->userAgent()
            );

            return $this->success('Invitation updated successfully.');
        } catch (\Exception $e) {
            Log::error('Invitation update failed', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);

            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function parseResume(ParseResumeRequest $request)
    {
        addInfoLog('parseResume request');

        $file = $request->file('resume');
        $originalName = (string) $file->getClientOriginalName();
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $tempPath = $file->getRealPath();
        $promptCode = (string) ($request->input('prompt_code') ?? 'resume_parse');

        try {
            $data = $this->candidateInvitationService->parseResume($tempPath, $extension, $originalName, $promptCode);

            return $this->success('Resume parsed successfully.', $data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function resend(Request $request, $invitationId)
    {
        addInfoLog("Candidate Invitation resend request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $this->candidateInvitationService->resendInvitation(
                (int) $invitationId,
                $clientId,
                $user,
                (string) $request->ip(),
                (string) $request->userAgent()
            );

            return $this->success('Invitation resent successfully.');
        } catch (\Exception $e) {
            Log::error('Invitation resend failed', [
                'error' => $e->getMessage(),
                'invitation_id' => $invitationId,
            ]);

            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
