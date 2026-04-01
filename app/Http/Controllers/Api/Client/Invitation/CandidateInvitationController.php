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
use App\Http\Requests\Api\Client\Invitation\UpdateCandidateInvitationByTokenRequest;
use App\Services\CandidateInvitationsLogService;
use App\Services\CandidateInvitationService;
use App\Services\CandidateService;
use App\Services\CandidateServiceDataService;
use App\Services\CandidateServiceService;
use App\Services\ClientService;
use App\Services\ConfigurationService;
use App\Services\Ai\AiManager;
use App\Services\EmailQueueService;
use App\Services\EmailTemplateService;
use App\Services\PackageService;
use App\Services\PackageServiceService;
use App\Services\ServiceService;
use App\Services\ServicesFieldService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;

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
    protected PackageServiceService $packageServiceService;
    protected ServiceService $serviceService;
    protected ServicesFieldService $servicesFieldService;
    protected CandidateServiceService $candidateServiceService;
    protected CandidateServiceDataService $candidateServiceDataService;
    protected CandidateInvitationService $service;
    protected AiManager $aiManager;

    public function __construct(
        CandidateInvitationService $service,
        CandidateService $candidateService,
        PackageService $packageService,
        ClientService $clientService,
        EmailTemplateService $emailTemplateService,
        EmailQueueService $emailQueueService,
        CandidateInvitationsLogService $candidateInvitationsLogService,
        ConfigurationService $configurationService,
        PackageServiceService $packageServiceService,
        ServiceService $serviceService,
        ServicesFieldService $servicesFieldService,
        CandidateServiceService $candidateServiceService,
        CandidateServiceDataService $candidateServiceDataService,
        AiManager $aiManager
    ) {
        $this->service = $service;
        $this->candidateService = $candidateService;
        $this->packageService = $packageService;
        $this->clientService = $clientService;
        $this->emailTemplateService = $emailTemplateService;
        $this->emailQueueService = $emailQueueService;
        $this->candidateInvitationsLogService = $candidateInvitationsLogService;
        $this->configurationService = $configurationService;
        $this->packageServiceService = $packageServiceService;
        $this->serviceService = $serviceService;
        $this->servicesFieldService = $servicesFieldService;
        $this->candidateServiceService = $candidateServiceService;
        $this->candidateServiceDataService = $candidateServiceDataService;
        $this->aiManager = $aiManager;
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

                $this->candidateService->update(
                    $invitation->{$this->service->candidateId()},
                    [
                        $this->candidateService->status() => CandidateStatus::SENT->value,
                        $this->candidateService->invitationSentAt() => now(),
                    ]
                );
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

    public function showByToken(Request $request, $token)
    {
        $token = trim((string) $token);
        if ($token === '') {
            return $this->error('Invitation token is required.', 422);
        }

        $invitation = $this->service->query()
            ->where($this->service->invitationToken(), $token)
            ->with(['candidate', 'package'])
            ->first();

        if (!$invitation) {
            return $this->error('Invitation not found or token is invalid.', 404);
        }

        if ((string) ($invitation->{$this->service->status()} ?? '') === CandidateInvitationStatus::COMPLETED->value) {
            return $this->error('Invitation has already been completed.', 422);
        }

        if ((string) ($invitation->{$this->service->status()} ?? '') !== CandidateInvitationStatus::PENDING->value) {
            return $this->error('Invitation is not valid.', 422);
        }

        $expiresAt = $invitation->{$this->service->expiresAt()};
        if (!empty($expiresAt) && now()->greaterThan($expiresAt)) {
            return $this->error('Invitation has expired.', 410);
        }

        [$formData, $packageIds, $packageServices, $serviceIds, $serviceFieldsByServiceId, $serviceMetaByServiceId] = $this->buildInvitationContext($invitation);
        $candidate = $invitation->candidate;
        $fieldValuesByFieldId = $this->getCandidateFieldValuesByFieldId(
            (int) ($candidate?->{$this->candidateService->id()} ?? 0),
            $serviceIds
        );

        $services = collect();
        if ($serviceIds->isNotEmpty()) {
            $services = $this->serviceService->query()
                ->whereIn($this->serviceService->id(), $serviceIds->all())
                ->where(function ($query) {
                    $query->where($this->serviceService->status(), 'active')
                        ->orWhere($this->serviceService->status(), 1);
                })
                ->get()
                ->map(function ($service) use ($serviceFieldsByServiceId, $serviceMetaByServiceId, $fieldValuesByFieldId) {
                    $serviceId = (int) ($service->{$this->serviceService->id()} ?? 0);
                    $fields = collect($serviceFieldsByServiceId->get($serviceId, []))
                        ->map(function ($field) use ($fieldValuesByFieldId) {
                            return [
                                'id' => $field->{$this->servicesFieldService->id()},
                                'service_id' => $field->{$this->servicesFieldService->serviceId()},
                                'field_name' => $field->{$this->servicesFieldService->fieldName()},
                                'field_label' => $field->{$this->servicesFieldService->fieldLabel()},
                                'field_type' => $field->{$this->servicesFieldService->fieldType()},
                                'is_required' => $field->{$this->servicesFieldService->isRequired()},
                                'validation_regex' => $field->{$this->servicesFieldService->validationRegex()},
                                'display_order' => $field->{$this->servicesFieldService->displayOrder()},
                                'status' => $field->{$this->servicesFieldService->status()},
                                'value' => $fieldValuesByFieldId->get((int) ($field->{$this->servicesFieldService->id()} ?? 0)),
                            ];
                        })
                        ->values()
                        ->all();

                    return array_merge([
                        'id' => $service->{$this->serviceService->id()},
                        'service_name' => $service->{$this->serviceService->serviceName()},
                        'service_code' => $service->{$this->serviceService->serviceCode()},
                        'service_category' => $service->{$this->serviceService->serviceCategory()},
                        'description' => $service->{$this->serviceService->description()},
                        'base_price' => $service->{$this->serviceService->basePrice()},
                        'status' => $service->{$this->serviceService->status()},
                        'fields' => $fields,
                    ], $serviceMetaByServiceId->get($serviceId, []));
                })
                ->sortBy('display_order')
                ->values();
        }

        $invitationData = $invitation->toArray();
        $invitationData['package_ids'] = $packageIds->values()->all();
        $invitationData['form_data'] = $formData;
        $candidateData = $candidate?->toArray();

        $invitationData['candidate'] = $candidateData;
        $invitationData['fields'] = $services->pluck('fields')->flatten(1)->values()->all();
        $invitationData['services'] = $services->values()->all();

        return $this->success('Invitation fetched successfully.', $invitationData);
    }

    public function updateByToken(Request $request, $token)
    {
        $token = trim((string) $token);
        if ($token === '') {
            return $this->error('Invitation token is required.', 422);
        }

        $invitation = $this->service->query()
            ->where($this->service->invitationToken(), $token)
            ->with(['candidate'])
            ->first();

        if (!$invitation) {
            return $this->error('Invitation not found or token is invalid.', 404);
        }

        if ((string) ($invitation->{$this->service->status()} ?? '') === CandidateInvitationStatus::COMPLETED->value) {
            return $this->error('Invitation has already been completed.', 422);
        }

        if ((string) ($invitation->{$this->service->status()} ?? '') !== CandidateInvitationStatus::PENDING->value) {
            return $this->error('Invitation is not valid.', 422);
        }

        $expiresAt = $invitation->{$this->service->expiresAt()};
        if (!empty($expiresAt) && now()->greaterThan($expiresAt)) {
            return $this->error('Invitation has expired.', 410);
        }

        $payload = UpdateCandidateInvitationByTokenRequest::normalizePayload($request->all());
        $validator = Validator::make(
            $payload,
            (new UpdateCandidateInvitationByTokenRequest())->rules()
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $validated = $validator->validated();
        [$formData, $packageIds, $packageServices, $serviceIds, $serviceFieldsByServiceId] = $this->buildInvitationContext($invitation);
        $allowedFields = $serviceFieldsByServiceId
            ->flatten(1)
            ->keyBy($this->servicesFieldService->id());

        $inputFields = collect($validated['fields'] ?? []);
        $invalidFieldIds = $inputFields
            ->pluck('field_id')
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->filter(fn($id) => !$allowedFields->has($id))
            ->unique()
            ->values()
            ->all();

        if ($invalidFieldIds !== []) {
            return $this->validationError([
                'fields' => ['Some fields are invalid for this invitation.'],
                'invalid_field_ids' => $invalidFieldIds,
            ]);
        }

        $candidate = $invitation->candidate;
        if (!$candidate) {
            return $this->error('Candidate not found for this invitation.', 404);
        }

        $candidateId = (int) ($candidate->{$this->candidateService->id()} ?? 0);
        if ($candidateId <= 0) {
            return $this->error('Candidate not found for this invitation.', 404);
        }

        DB::transaction(function () use ($validated, $candidateId, $serviceIds, $allowedFields, $invitation, $request) {
            $candidateDetails = $validated['candidate_details'] ?? [];

            $this->candidateService->update($candidateId, [
                $this->candidateService->firstName() => trim((string) ($candidateDetails['first_name'] ?? '')),
                $this->candidateService->lastName() => trim((string) ($candidateDetails['last_name'] ?? '')),
                $this->candidateService->email() => strtolower(trim((string) ($candidateDetails['email'] ?? ''))),
                $this->candidateService->phone() => trim((string) ($candidateDetails['phone'] ?? '')),
                $this->candidateService->address() => trim((string) ($candidateDetails['address'] ?? '')),
                $this->candidateService->countryId() => !empty($candidateDetails['country_id']) ? (int) $candidateDetails['country_id'] : null,
                $this->candidateService->stateId() => !empty($candidateDetails['state_id']) ? (int) $candidateDetails['state_id'] : null,
                $this->candidateService->cityId() => !empty($candidateDetails['city_id']) ? (int) $candidateDetails['city_id'] : null,
                $this->candidateService->pincode() => !empty($candidateDetails['pincode']) ? (string) $candidateDetails['pincode'] : null,
                $this->candidateService->status() => CandidateStatus::ACTIVE->value,
                $this->candidateService->invitationAcceptedAt() => now(),
            ]);

            $candidateServices = collect();
            if ($serviceIds->isNotEmpty()) {
                $candidateServices = $this->candidateServiceService->query()
                    ->where($this->candidateServiceService->candidateId(), $candidateId)
                    ->whereIn($this->candidateServiceService->serviceId(), $serviceIds->all())
                    ->get()
                    ->keyBy($this->candidateServiceService->serviceId());
            }

            foreach (($validated['fields'] ?? []) as $fieldInput) {
                $fieldId = (int) ($fieldInput['field_id'] ?? 0);
                if ($fieldId <= 0) {
                    continue;
                }

                $field = $allowedFields->get($fieldId);
                if (!$field) {
                    continue;
                }

                $serviceId = (int) ($field->{$this->servicesFieldService->serviceId()} ?? 0);
                if ($serviceId <= 0) {
                    continue;
                }

                $candidateService = $candidateServices->get($serviceId);
                if (!$candidateService) {
                    $candidateService = $this->candidateServiceService->create([
                        $this->candidateServiceService->candidateId() => $candidateId,
                        $this->candidateServiceService->serviceId() => $serviceId,
                        $this->candidateServiceService->status() => CandidateStatus::ACTIVE->value,
                    ]);
                    $candidateServices->put($serviceId, $candidateService);
                }

                $candidateServiceId = (int) ($candidateService->{$this->candidateServiceService->id()} ?? 0);
                if ($candidateServiceId <= 0) {
                    continue;
                }

                $existingValue = $this->candidateServiceDataService->query()
                    ->where($this->candidateServiceDataService->candidateServiceId(), $candidateServiceId)
                    ->where($this->candidateServiceDataService->fieldId(), $fieldId)
                    ->first();

                $value = $fieldInput['value'] ?? null;
                $normalizedValue = is_scalar($value) || $value === null
                    ? $value
                    : json_encode($value);

                if ($existingValue) {
                    $this->candidateServiceDataService->update(
                        $existingValue->{$this->candidateServiceDataService->id()},
                        [
                            $this->candidateServiceDataService->fieldValue() => $normalizedValue,
                        ]
                    );
                    continue;
                }

                $this->candidateServiceDataService->create([
                    $this->candidateServiceDataService->candidateServiceId() => $candidateServiceId,
                    $this->candidateServiceDataService->fieldId() => $fieldId,
                    $this->candidateServiceDataService->fieldValue() => $normalizedValue,
                    $this->candidateServiceDataService->status() => CandidateStatus::ACTIVE->value,
                ]);
            }

            $this->service->update(
                $invitation->{$this->service->id()},
                [
                    $this->service->status() => CandidateInvitationStatus::COMPLETED->value,
                    $this->service->completedAt() => now(),
                ]
            );

            $this->candidateInvitationsLogService->create([
                $this->candidateInvitationsLogService->invitationId() => $invitation->{$this->service->id()},
                $this->candidateInvitationsLogService->action() => 'completed',
                $this->candidateInvitationsLogService->ipAddress() => $request->ip(),
                $this->candidateInvitationsLogService->userAgent() => $request->userAgent(),
                $this->candidateInvitationsLogService->status() => CandidateInvitationStatus::COMPLETED->value,
            ]);
        });

        return $this->success('Invitation updated successfully.');
    }

    protected function buildInvitationContext($invitation): array
    {
        $formData = $invitation->{$this->service->formData()} ?? [];
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
            ->values();

        if ($packageIds->isEmpty()) {
            $primaryPackageId = (int) ($invitation->{$this->service->packageId()} ?? 0);
            if ($primaryPackageId > 0) {
                $packageIds = collect([$primaryPackageId]);
            }
        }

        $packageServices = collect();
        $serviceIds = collect();
        if ($packageIds->isNotEmpty()) {
            $packageServices = $this->packageServiceService->query()
                ->whereIn($this->packageServiceService->packageId(), $packageIds->all())
                ->where(function ($query) {
                    $query->where($this->packageServiceService->status(), 'active')
                        ->orWhere($this->packageServiceService->status(), 1);
                })
                ->orderBy($this->packageServiceService->displayOrder(), 'asc')
                ->get();

            $serviceIds = $packageServices
                ->pluck($this->packageServiceService->serviceId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values();
        }

        $serviceFieldsByServiceId = collect();
        if ($serviceIds->isNotEmpty()) {
            $serviceFieldsByServiceId = $this->servicesFieldService->query()
                ->whereIn($this->servicesFieldService->serviceId(), $serviceIds->all())
                ->where(function ($query) {
                    $query->where($this->servicesFieldService->status(), 'active')
                        ->orWhere($this->servicesFieldService->status(), 1);
                })
                ->orderBy($this->servicesFieldService->displayOrder(), 'asc')
                ->get()
                ->groupBy($this->servicesFieldService->serviceId());
        }

        $serviceMetaByServiceId = $packageServices
            ->groupBy($this->packageServiceService->serviceId())
            ->map(function ($rows) {
                $first = $rows->first();

                return [
                    'package_ids' => $rows->pluck($this->packageServiceService->packageId())
                        ->map(static fn($id) => (int) $id)
                        ->filter(static fn($id) => $id > 0)
                        ->unique()
                        ->values()
                        ->all(),
                    'is_mandatory' => $first?->{$this->packageServiceService->isMandatory()},
                    'price_override' => $first?->{$this->packageServiceService->priceOverride()},
                    'display_order' => $first?->{$this->packageServiceService->displayOrder()},
                ];
            });

        return [$formData, $packageIds, $packageServices, $serviceIds, $serviceFieldsByServiceId, $serviceMetaByServiceId];
    }

    protected function getCandidateFieldValuesByFieldId(int $candidateId, $serviceIds)
    {
        if ($candidateId <= 0 || $serviceIds->isEmpty()) {
            return collect();
        }

        $candidateServices = $this->candidateServiceService->query()
            ->where($this->candidateServiceService->candidateId(), $candidateId)
            ->whereIn($this->candidateServiceService->serviceId(), $serviceIds->all())
            ->get();

        if ($candidateServices->isEmpty()) {
            return collect();
        }

        $candidateServiceIds = $candidateServices
            ->pluck($this->candidateServiceService->id())
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->values()
            ->all();

        if ($candidateServiceIds === []) {
            return collect();
        }

        return $this->candidateServiceDataService->query()
            ->whereIn($this->candidateServiceDataService->candidateServiceId(), $candidateServiceIds)
            ->orderByDesc($this->candidateServiceDataService->id())
            ->get()
            ->unique($this->candidateServiceDataService->fieldId())
            ->keyBy($this->candidateServiceDataService->fieldId())
            ->map(fn($row) => $row->{$this->candidateServiceDataService->fieldValue()});
    }

    public function parseResume(Request $request, \App\Services\Ai\ResumeParserService $parserService)
    {
        addInfoLog('parseResume request');

        $validator = Validator::make($request->all(), [
            'resume' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:10240'],
            'prompt_code' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            addErrorLog('Resume validation failed', $validator->errors()->toArray());
            return $this->validationError($validator->errors()->toArray());
        }

        $file = $request->file('resume');
        if (!$file) {
            addErrorLog('Resume file not found in request');
            return $this->error('Resume file not found.', 422);
        }

        $originalName = (string) $file->getClientOriginalName();
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $tempPath = $file->getRealPath();

        if (!$tempPath || !file_exists($tempPath)) {
            addErrorLog('Uploaded file could not be accessed', ['temp_path' => $tempPath]);
            return $this->error('Uploaded file could not be accessed.', 422);
        }

        try {
            $promptCode = (string) ($request->input('prompt_code') ?? 'resume_parse');
            
            $result = $parserService->parseResumeFile($tempPath, $extension, $originalName, $promptCode);
            
            if (!$result['success']) {
                return $this->error($result['error'], $result['status_code'] ?? 422, $result['details'] ?? []);
            }

            return $this->success('Resume parsed successfully.', $result['data']);
        } finally {
            $this->cleanupTempFile($tempPath);
        }
    }

    protected function cleanupTempFile(?string $path): void
    {
        if (!$path || !is_file($path)) {
            return;
        }

        @unlink($path);
    }
}
