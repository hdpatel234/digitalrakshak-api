<?php
namespace App\Http\Controllers\Api\Client\Invitation;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Invitation\StoreCandidateInvitationRequest;
use App\Services\CandidateInvitationsLogService;
use App\Services\CandidateInvitationService;
use App\Services\CandidateService;
use App\Services\PackageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateInvitationController extends BaseController
{
    use ApiResponse;
    protected CandidateService $candidateService;
    protected PackageService $packageService;
    protected CandidateInvitationsLogService $candidateInvitationsLogService;
    protected CandidateInvitationService $service;

    public function __construct(
        CandidateInvitationService $service,
        CandidateService $candidateService,
        PackageService $packageService,
        CandidateInvitationsLogService $candidateInvitationsLogService
    ) {
        $this->service = $service;
        $this->candidateService = $candidateService;
        $this->packageService = $packageService;
        $this->candidateInvitationsLogService = $candidateInvitationsLogService;
    }

    public function index(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $invitationTable = $this->service->query()->getModel()->getTable();
        $candidateTable = $this->candidateService->query()->getModel()->getTable();
        $packageTable = $this->packageService->query()->getModel()->getTable();

        $invitationIdColumn = $this->service->id();
        $candidateIdColumn = $this->service->candidateId();
        $packageIdColumn = $this->service->packageId();
        $clientIdColumn = $this->service->clientId();
        $statusColumn = $this->service->status();
        $formDataColumn = $this->service->formData();

        $query = $this->service->query()
            ->where($invitationTable . '.' . $clientIdColumn, $clientId)
            ->leftJoin(
                $candidateTable,
                $candidateTable . '.' . $this->candidateService->id(),
                '=',
                $invitationTable . '.' . $candidateIdColumn
            )
            ->leftJoin(
                $packageTable,
                $packageTable . '.' . $this->packageService->id(),
                '=',
                $invitationTable . '.' . $packageIdColumn
            )
            ->select($invitationTable . '.*')
            ->addSelect($candidateTable . '.' . $this->candidateService->firstName() . ' as candidate_first_name')
            ->addSelect($candidateTable . '.' . $this->candidateService->lastName() . ' as candidate_last_name')
            ->addSelect($candidateTable . '.' . $this->candidateService->email() . ' as candidate_email')
            ->addSelect($candidateTable . '.' . $this->candidateService->phone() . ' as candidate_phone')
            ->addSelect($packageTable . '.' . $this->packageService->packageName() . ' as primary_package_name')
            ->addSelect($packageTable . '.' . $this->packageService->packageCode() . ' as primary_package_code');

        $result = $this->service->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
                    $candidateTable . '.' . $this->candidateService->firstName(),
                    $candidateTable . '.' . $this->candidateService->lastName(),
                    $candidateTable . '.' . $this->candidateService->email(),
                    $packageTable . '.' . $this->packageService->packageName(),
                    $packageTable . '.' . $this->packageService->packageCode(),
                    $invitationTable . '.' . $this->service->invitationToken(),
                    $invitationTable . '.' . $this->service->formLink(),
                    $invitationTable . '.' . $this->service->invitationType(),
                ],
                'status_column' => $invitationTable . '.' . $statusColumn,
                'date_column' => $invitationTable . '.' . $this->service->createdAt(),
                'allowed_filters' => [
                    'candidate_id' => $invitationTable . '.' . $candidateIdColumn,
                    'package_id' => $invitationTable . '.' . $packageIdColumn,
                    'invitation_type' => $invitationTable . '.' . $this->service->invitationType(),
                    'invited_by' => $invitationTable . '.' . $this->service->invitedBy(),
                ],
                'allowed_sorts' => [
                    $invitationTable . '.' . $invitationIdColumn,
                    $invitationTable . '.' . $statusColumn,
                    $invitationTable . '.' . $this->service->invitedAt(),
                    $invitationTable . '.' . $this->service->expiresAt(),
                    $invitationTable . '.' . $this->service->createdAt(),
                    $candidateTable . '.' . $this->candidateService->firstName(),
                    $candidateTable . '.' . $this->candidateService->lastName(),
                    $candidateTable . '.' . $this->candidateService->email(),
                    $packageTable . '.' . $this->packageService->packageName(),
                ],
                'default_sort_by' => $invitationTable . '.' . $this->service->createdAt(),
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $normalized = collect($result['list'])
            ->map(function ($item) use ($packageIdColumn, $formDataColumn) {
                $invitation = is_array($item) ? $item : $item->toArray();

                $formData = $invitation[$formDataColumn] ?? [];
                if (is_string($formData)) {
                    $decoded = json_decode($formData, true);
                    $formData = is_array($decoded) ? $decoded : [];
                }

                if (!is_array($formData)) {
                    $formData = [];
                }

                $packageIds = collect($formData['package_ids'] ?? [])
                    ->map(static fn($id) => (int) $id)
                    ->filter(static fn($id) => $id > 0)
                    ->unique()
                    ->values()
                    ->all();

                if ($packageIds === [] && !empty($invitation[$packageIdColumn])) {
                    $packageIds = [(int) $invitation[$packageIdColumn]];
                }

                $candidateFirstName = trim((string) ($invitation['candidate_first_name'] ?? ''));
                $candidateLastName = trim((string) ($invitation['candidate_last_name'] ?? ''));

                $invitation['candidate_name'] = trim($candidateFirstName . ' ' . $candidateLastName);
                $invitation['package_ids'] = $packageIds;
                $invitation['primary_package'] = [
                    'id' => $invitation[$packageIdColumn] ?? null,
                    'name' => $invitation['primary_package_name'] ?? null,
                    'code' => $invitation['primary_package_code'] ?? null,
                ];

                return $invitation;
            })
            ->values()
            ->all();

        $result['list'] = $normalized;

        return $this->success('Invitations fetched successfully.', $result);
    }

    public function store(StoreCandidateInvitationRequest $request)
    {
        return $this->createInvitations($request, $request->validated());
    }

    public function invite(StoreCandidateInvitationRequest $request, int $candidate)
    {
        $payload = $request->validated();
        $payload['candidate_ids'] = [$candidate];

        return $this->createInvitations($request, $payload);
    }

    protected function createInvitations(Request $request, array $payload)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $candidateIds = collect($payload['candidate_ids'] ?? [])
            ->map(static fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
        $packageIds = collect($payload['package_ids'] ?? [])
            ->map(static fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $candidateIdColumn = $this->candidateService->id();
        $candidates = $this->candidateService->query()
            ->where($this->candidateService->clientId(), $clientId)
            ->whereIn($candidateIdColumn, $candidateIds)
            ->get();

        $foundCandidateIds = $candidates->pluck($candidateIdColumn)->map(static fn($id) => (int) $id)->all();
        $invalidCandidateIds = array_values(array_diff($candidateIds, $foundCandidateIds));

        if ($invalidCandidateIds !== []) {
            return $this->validationError([
                'candidate_ids' => ['Some candidates are invalid for this client.'],
                'invalid_candidate_ids' => $invalidCandidateIds,
            ]);
        }

        $packageIdColumn = $this->packageService->id();
        $packageClientIdColumn = $this->packageService->clientId();
        $packageStatusColumn = $this->packageService->status();
        $packageIsActiveColumn = $this->packageService->isActive();

        $packages = $this->packageService->query()
            ->whereIn($packageIdColumn, $packageIds)
            ->where(function ($query) use ($packageClientIdColumn, $clientId) {
                $query->where($packageClientIdColumn, $clientId)
                    ->orWhere($packageClientIdColumn, 0);
            })
            ->where(function ($query) use ($packageStatusColumn) {
                $query->where($packageStatusColumn, 'active')
                    ->orWhere($packageStatusColumn, 1);
            })
            ->where(function ($query) use ($packageIsActiveColumn) {
                $query->where($packageIsActiveColumn, 'active')
                    ->orWhere($packageIsActiveColumn, 1);
            })
            ->get();

        $foundPackageIds = $packages->pluck($packageIdColumn)->map(static fn($id) => (int) $id)->all();
        $invalidPackageIds = array_values(array_diff($packageIds, $foundPackageIds));

        if ($invalidPackageIds !== []) {
            return $this->validationError([
                'package_ids' => ['Some packages are invalid or inactive for this client.'],
                'invalid_package_ids' => $invalidPackageIds,
            ]);
        }

        $primaryPackageId = $packageIds[0] ?? null;
        $now = now();
        $expiresAt = !empty($payload['expires_at']) ? $payload['expires_at'] : $now->copy()->addDays(7)->toDateTimeString();
        $invitationType = (string) ($payload['invitation_type'] ?? 'email');
        $status = (string) ($payload['status'] ?? 'pending');
        $baseFormData = is_array($payload['form_data'] ?? null) ? $payload['form_data'] : [];

        $createdInvitations = DB::transaction(function () use ($candidates, $packageIds, $primaryPackageId, $baseFormData, $invitationType, $status, $expiresAt, $now, $user, $request, $clientId) {
            $invitations = [];

            foreach ($candidates as $candidate) {
                $token = Str::random(64);
                $formLink = '/invitation/' . $token;

                $invitation = $this->service->create([
                    $this->service->candidateId() => $candidate->{$this->candidateService->id()},
                    $this->service->clientId() => $clientId,
                    $this->service->packageId() => $primaryPackageId,
                    $this->service->invitationType() => $invitationType,
                    $this->service->invitationToken() => $token,
                    $this->service->formLink() => $formLink,
                    $this->service->formData() => array_merge($baseFormData, [
                        'package_ids' => $packageIds,
                        'candidate_id' => $candidate->{$this->candidateService->id()},
                    ]),
                    $this->service->invitedBy() => $user?->id,
                    $this->service->invitedAt() => $now,
                    $this->service->expiresAt() => $expiresAt,
                    $this->service->reminderCount() => 0,
                    $this->service->status() => $status,
                ]);

                $this->candidateInvitationsLogService->create([
                    $this->candidateInvitationsLogService->invitationId() => $invitation->{$this->service->id()},
                    $this->candidateInvitationsLogService->action() => 'created',
                    $this->candidateInvitationsLogService->ipAddress() => $request->ip(),
                    $this->candidateInvitationsLogService->userAgent() => $request->userAgent(),
                    $this->candidateInvitationsLogService->status() => $status,
                ]);

                $this->candidateService->update(
                    $candidate->{$this->candidateService->id()},
                    [
                        $this->candidateService->status() => CandidateStatus::INVITED->value,
                    ]
                );

                $invitations[] = $invitation;
            }

            $this->candidateService->query()
                ->whereIn($this->candidateService->id(), $candidates->pluck($this->candidateService->id()))
                ->update([
                    $this->candidateService->invitationSentAt() => $now,
                ]);

            return $invitations;
        });

        $responseInvitations = collect($createdInvitations)
            ->map(function ($invitation) use ($packageIds) {
                return [
                    'id' => $invitation->{$this->service->id()},
                    'candidate_id' => $invitation->{$this->service->candidateId()},
                    'package_id' => $invitation->{$this->service->packageId()},
                    'package_ids' => $packageIds,
                    'invitation_type' => $invitation->{$this->service->invitationType()},
                    'invitation_token' => $invitation->{$this->service->invitationToken()},
                    'form_link' => $invitation->{$this->service->formLink()},
                    'status' => $invitation->{$this->service->status()},
                    'invited_at' => $invitation->{$this->service->invitedAt()},
                    'expires_at' => $invitation->{$this->service->expiresAt()},
                ];
            })
            ->values()
            ->all();

        return $this->success('Candidate invitations created successfully.', [
            'candidate_ids' => $candidateIds,
            'package_ids' => $packageIds,
            'total_candidates' => count($candidateIds),
            'total_packages' => count($packageIds),
            'total_invitations' => count($responseInvitations),
            'invitations' => $responseInvitations,
        ], 201);
    }

    // public function update(CandidateInvitationRequest $request, CandidateInvitationService $service)
    // {

    // }

    // public function destroy(CandidateInvitationService $service)
    // {

    // }
}
