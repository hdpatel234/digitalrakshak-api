<?php
namespace App\Http\Controllers\Api\Client\Invitation;

use App\Enums\CandidateInvitationStatus;
use App\Enums\CandidateInvitationType;
use App\Enums\CandidateStatus;
use App\Enums\ConfigurationKey;
use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Enums\EmailTemplateCode;
use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Invitation\StoreCandidateInvitationRequest;
use App\Services\CandidateInvitationsLogService;
use App\Services\CandidateInvitationService;
use App\Services\CandidateService;
use App\Services\ClientService;
use App\Services\ConfigurationService;
use App\Services\EmailQueueService;
use App\Services\EmailTemplateService;
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
    protected ClientService $clientService;
    protected EmailTemplateService $emailTemplateService;
    protected EmailQueueService $emailQueueService;
    protected CandidateInvitationsLogService $candidateInvitationsLogService;
    protected ConfigurationService $configurationService;
    protected CandidateInvitationService $service;

    public function __construct(
        CandidateInvitationService $service,
        CandidateService $candidateService,
        PackageService $packageService,
        ClientService $clientService,
        EmailTemplateService $emailTemplateService,
        EmailQueueService $emailQueueService,
        CandidateInvitationsLogService $candidateInvitationsLogService,
        ConfigurationService $configurationService
    ) {
        $this->service = $service;
        $this->candidateService = $candidateService;
        $this->packageService = $packageService;
        $this->clientService = $clientService;
        $this->emailTemplateService = $emailTemplateService;
        $this->emailQueueService = $emailQueueService;
        $this->candidateInvitationsLogService = $candidateInvitationsLogService;
        $this->configurationService = $configurationService;
    }

    public function index(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $invitationTable = $this->service->query()->getModel()->getTable();

        $invitationIdColumn = $this->service->id();
        $candidateIdColumn = $this->service->candidateId();
        $packageIdColumn = $this->service->packageId();
        $clientIdColumn = $this->service->clientId();
        $statusColumn = $this->service->status();
        $formDataColumn = $this->service->formData();

        $query = $this->service->query()
            ->where($invitationTable . '.' . $clientIdColumn, $clientId);

        $result = $this->service->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
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
                ],
                'default_sort_by' => $invitationTable . '.' . $this->service->createdAt(),
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $list = collect($result['list'])
            ->map(static fn($item) => is_array($item) ? $item : $item->toArray())
            ->values();

        $invitationIds = $list
            ->pluck($invitationIdColumn)
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->all();

        $invitationsById = $this->service->query()
            ->whereIn($invitationIdColumn, $invitationIds)
            ->with(['candidate', 'package'])
            ->get()
            ->keyBy($invitationIdColumn);

        $normalized = $list
            ->map(function ($item) use ($packageIdColumn, $formDataColumn, $invitationsById, $invitationIdColumn) {
                $invitation = $item;
                $invitationModel = $invitationsById->get((int) ($invitation[$invitationIdColumn] ?? 0));

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

                $candidate = $invitationModel?->candidate;
                $package = $invitationModel?->package;
                $candidateFirstName = trim((string) ($candidate?->{$this->candidateService->firstName()} ?? ''));
                $candidateLastName = trim((string) ($candidate?->{$this->candidateService->lastName()} ?? ''));

                $invitation['candidate'] = [
                    'id' => $candidate?->{$this->candidateService->id()} ?? ($invitation[$this->service->candidateId()] ?? null),
                    'name' => trim($candidateFirstName . ' ' . $candidateLastName),
                    'first_name' => $candidate?->{$this->candidateService->firstName()} ?? null,
                    'last_name' => $candidate?->{$this->candidateService->lastName()} ?? null,
                    'email' => $candidate?->{$this->candidateService->email()} ?? null,
                    'phone' => $candidate?->{$this->candidateService->phone()} ?? null,
                ];

                $invitation['package'] = [
                    'id' => $package?->{$this->packageService->id()} ?? ($invitation[$packageIdColumn] ?? null),
                    'name' => $package?->{$this->packageService->packageName()} ?? null,
                    'code' => $package?->{$this->packageService->packageCode()} ?? null,
                    'package_ids' => $packageIds,
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
        $defaultExpiryDays = max(1, $this->configurationService->getIntValue(ConfigurationKey::INVITATION_LINK_EXPIRY_DAYS, 7));
        $clientAppUrl = $this->configurationService->getStringValue(
            ConfigurationKey::CLIENT_APP_URL,
            (string) config('app.client_url', env('CLIENT_URL', ''))
        );
        $now = now();
        $expiresAt = !empty($payload['expires_at'])
            ? $payload['expires_at']
            : $now->copy()->addDays($defaultExpiryDays)->toDateTimeString();
        $invitationType = (string) ($payload['invitation_type'] ?? CandidateInvitationType::EMAIL->value);
        $status = (string) ($payload['status'] ?? CandidateInvitationStatus::PENDING->value);
        $baseFormData = is_array($payload['form_data'] ?? null) ? $payload['form_data'] : [];

        $client = $this->clientService->query()
            ->where($this->clientService->id(), $clientId)
            ->first();

        $invitationTemplate = $this->emailTemplateService->findActiveByCode(
            EmailTemplateCode::CANDIDATE_INVITATION_FORM->value
        );

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

        if ($invitationTemplate) {
            foreach ($createdInvitations as $invitation) {
                $candidate = $candidates->firstWhere(
                    $this->candidateService->id(),
                    $invitation->{$this->service->candidateId()}
                );

                $candidateEmail = strtolower(trim((string) ($candidate?->{$this->candidateService->email()} ?? '')));
                if ($candidateEmail === '') {
                    continue;
                }

                $candidateFirstName = trim((string) ($candidate?->{$this->candidateService->firstName()} ?? ''));
                $candidateLastName = trim((string) ($candidate?->{$this->candidateService->lastName()} ?? ''));
                $candidateFullName = trim($candidateFirstName . ' ' . $candidateLastName) ?? $candidateEmail;

                $relativeLink = (string) ($invitation->{$this->service->formLink()} ?? '');
                $baseUrl = rtrim($clientAppUrl, '/');
                $inviteLink = $baseUrl !== ''
                    ? $baseUrl . '/' . ltrim($relativeLink, '/')
                    : '/' . ltrim($relativeLink, '/');

                $rendered = $this->emailTemplateService->renderTemplate($invitationTemplate, [
                    'candidate_full_name' => $candidateFullName,
                    'candidate_first_name' => $candidateFirstName,
                    'candidate_last_name' => $candidateLastName,
                    'candidate_email' => $candidateEmail,
                    'candidate_invite_link' => $inviteLink,
                    'company_name' => (string) ($client?->{$this->clientService->companyName()} ?? config('app.name')),
                    'client_email' => (string) ($client?->{$this->clientService->email()} ?? ''),
                    'invitation_token' => (string) ($invitation->{$this->service->invitationToken()} ?? ''),
                    'invitation_expires_at' => (string) ($invitation->{$this->service->expiresAt()} ?? ''),
                ]);

                $this->emailQueueService->create([
                    $this->emailQueueService->emailUid() => 'email_' . Str::uuid(),
                    $this->emailQueueService->toEmail() => $candidateEmail,
                    $this->emailQueueService->toName() => $candidateFullName !== '' ? $candidateFullName : null,
                    $this->emailQueueService->subject() => (string) ($rendered['subject'] ?? ''),
                    $this->emailQueueService->bodyHtml() => $rendered['body_html'] ?? null,
                    $this->emailQueueService->bodyText() => $rendered['body_text'] ?? null,
                    $this->emailQueueService->templateId() => $invitationTemplate->{$this->emailTemplateService->id()},
                    $this->emailQueueService->emailType() => (string) ($invitationTemplate->{$this->emailTemplateService->emailType()} ?? 'candidate_invitation'),
                    $this->emailQueueService->priority() => (string) ($invitationTemplate->{$this->emailTemplateService->defaultPriority()} ?? EmailPriority::NORMAL->value),
                    $this->emailQueueService->clientId() => $clientId,
                    $this->emailQueueService->candidateId() => $invitation->{$this->service->candidateId()},
                    $this->emailQueueService->userId() => $user?->id,
                    $this->emailQueueService->assignedServerId() => $invitationTemplate->{$this->emailTemplateService->serverId()},
                    $this->emailQueueService->status() => EmailQueueStatus::PENDING->value,
                    $this->emailQueueService->attempts() => 0,
                    $this->emailQueueService->maxAttempts() => 3,
                    $this->emailQueueService->scheduledAt() => now(),
                    $this->emailQueueService->expiresAt() => $invitation->{$this->service->expiresAt()},
                ]);
            }
        } else {
            Log::warning('Candidate invitation template not found.', [
                'template_code' => EmailTemplateCode::CANDIDATE_INVITATION_FORM->value,
                'client_id' => $clientId,
            ]);
        }

        $createdInvitationIds = collect($createdInvitations)
            ->pluck($this->service->id())
            ->map(static fn($id) => (int) $id)
            ->values()
            ->all();

        $createdInvitationWithRelations = $this->service->query()
            ->whereIn($this->service->id(), $createdInvitationIds)
            ->with(['candidate', 'package'])
            ->get();

        $responseInvitations = $createdInvitationWithRelations
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

        $responseCandidates = $createdInvitationWithRelations
            ->pluck('candidate')
            ->filter()
            ->unique($this->candidateService->id())
            ->map(function ($candidate) {
                return [
                    'id' => $candidate->{$this->candidateService->id()},
                    'first_name' => $candidate->{$this->candidateService->firstName()},
                    'last_name' => $candidate->{$this->candidateService->lastName()},
                    'email' => $candidate->{$this->candidateService->email()},
                    'phone' => $candidate->{$this->candidateService->phone()},
                    'status' => $candidate->{$this->candidateService->status()},
                ];
            })
            ->values()
            ->all();

        $responsePackages = $packages
            ->map(function ($package) {
                return [
                    'id' => $package->{$this->packageService->id()},
                    'package_name' => $package->{$this->packageService->packageName()},
                    'package_code' => $package->{$this->packageService->packageCode()},
                    'type' => $package->{$this->packageService->type()},
                    'status' => $package->{$this->packageService->status()},
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
            'candidates' => $responseCandidates,
            'packages' => $responsePackages,
        ], 201);
    }
}
