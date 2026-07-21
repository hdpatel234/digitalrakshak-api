<?php

namespace App\Services\ApiService\Client;

use App\Enums\CandidateInvitationStatus;
use App\Enums\CandidateInvitationType;
use App\Enums\CandidateStatus;
use App\Enums\ConfigurationKey;
use App\Enums\EmailPriority;
use App\Enums\EmailQueueStatus;
use App\Enums\EmailTemplateCode;
use App\Services\BaseService;
use App\Services\ConfigurationService;
use App\Services\EmailTemplateService;
use App\Repositories\CandidateInvitationRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\PackageRepository;
use App\Repositories\ClientRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\EmailQueueRepository;
use App\Repositories\CandidateInvitationsLogRepository;
use App\Repositories\PackageServiceRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServicesFieldRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\CandidateServiceDataRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\Common\ResumeParserService;

/**
 * @property CandidateInvitationRepository $invitationRepo
 */
class CandidateInvitationService extends BaseService
{
    public function __construct(
        protected CandidateInvitationRepository $invitationRepo,
        protected CandidateRepository $candidateRepo,
        protected PackageRepository $packageRepo,
        protected ClientRepository $clientRepo,
        protected EmailTemplateRepository $emailTemplateRepo,
        protected EmailTemplateService $emailTemplateService,
        protected EmailQueueRepository $emailQueueRepo,
        protected CandidateInvitationsLogRepository $candidateInvitationsLogRepo,
        protected ConfigurationService $configurationService,
        protected PackageServiceRepository $packageServiceRepo,
        protected ServiceRepository $serviceRepo,
        protected ServicesFieldRepository $servicesFieldRepo,
        protected OrderItemRepository $orderItemRepo,
        protected CandidateServiceDataRepository $candidateServiceDataRepo,
        protected ResumeParserService $resumeParserService
    ) {}

    public function getInvitations(array $params, int $clientId): array
    {
        $invitationTable = $this->invitationRepo->query()->getModel()->getTable();
        $invitationIdColumn = $this->invitationRepo->id();
        $candidateIdColumn = $this->invitationRepo->candidateId();
        $packageIdColumn = $this->invitationRepo->packageId();
        $clientIdColumn = $this->invitationRepo->clientId();
        $statusColumn = $this->invitationRepo->status();

        $query = $this->invitationRepo->query()
            ->where($invitationTable . '.' . $clientIdColumn, $clientId);

        $result = $this->invitationRepo->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $invitationTable . '.' . $this->invitationRepo->invitationToken(),
                    $invitationTable . '.' . $this->invitationRepo->formLink(),
                    $invitationTable . '.' . $this->invitationRepo->invitationType(),
                ],
                'status_column' => $invitationTable . '.' . $statusColumn,
                'date_column' => $invitationTable . '.' . $this->invitationRepo->createdAt(),
                'allowed_filters' => [
                    'candidate_id' => $invitationTable . '.' . $candidateIdColumn,
                    'package_id' => $invitationTable . '.' . $packageIdColumn,
                    'invitation_type' => $invitationTable . '.' . $this->invitationRepo->invitationType(),
                    'invited_by' => $invitationTable . '.' . $this->invitationRepo->invitedBy(),
                ],
                'allowed_sorts' => [
                    $invitationTable . '.' . $invitationIdColumn,
                    $invitationTable . '.' . $statusColumn,
                    $invitationTable . '.' . $this->invitationRepo->invitedAt(),
                    $invitationTable . '.' . $this->invitationRepo->expiresAt(),
                    $invitationTable . '.' . $this->invitationRepo->createdAt(),
                ],
                'default_sort_by' => $invitationTable . '.' . $this->invitationRepo->createdAt(),
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

        $invitationsById = $this->invitationRepo->query()
            ->whereIn($invitationIdColumn, $invitationIds)
            ->with(['candidate', 'package'])
            ->get()
            ->keyBy($invitationIdColumn);

        $allPackageIds = collect($list)->map(function ($item) use ($packageIdColumn) {
            $ids = collect($item[$packageIdColumn] ?? [])
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();
            return $ids;
        })->flatten()->unique()->values()->all();

        $packagesById = collect();
        if (!empty($allPackageIds)) {
            $packagesById = $this->packageRepo->query()
                ->whereIn($this->packageRepo->id(), $allPackageIds)
                ->get()
                ->keyBy($this->packageRepo->id());
        }

        $clientAppUrl = $this->configurationService->getStringValue(
            ConfigurationKey::CLIENT_APP_URL,
            (string) config('app.client_url', env('CLIENT_URL', ''))
        );
        $baseUrl = rtrim($clientAppUrl, '/');

        $normalized = $list
            ->map(function ($item) use ($packageIdColumn, $invitationsById, $invitationIdColumn, $baseUrl, $packagesById) {
                $invitation = $item;
                $invitationModel = $invitationsById->get((int) ($invitation[$invitationIdColumn] ?? 0));

                $packageIds = collect($item[$packageIdColumn] ?? [])
                    ->map(static fn($id) => (int) $id)
                    ->filter(static fn($id) => $id > 0)
                    ->unique()
                    ->values()
                    ->all();

                $candidate = $invitationModel?->candidate;
                $package = $invitationModel?->package;
                $candidateFirstName = trim((string) ($candidate?->{$this->candidateRepo->firstName()} ?? ''));
                $candidateLastName = trim((string) ($candidate?->{$this->candidateRepo->lastName()} ?? ''));

                $invitation['candidate'] = [
                    'id' => $candidate?->{$this->candidateRepo->id()} ?? ($invitation[$this->invitationRepo->candidateId()] ?? null),
                    'name' => trim($candidateFirstName . ' ' . $candidateLastName),
                    'first_name' => $candidate?->{$this->candidateRepo->firstName()} ?? null,
                    'last_name' => $candidate?->{$this->candidateRepo->lastName()} ?? null,
                    'email' => $candidate?->{$this->candidateRepo->email()} ?? null,
                    'phone' => $candidate?->{$this->candidateRepo->phone()} ?? null,
                ];

                $invitation['package'] = [
                    'id' => $package?->{$this->packageRepo->id()} ?? ($invitation[$packageIdColumn] ?? null),
                    'name' => $package?->{$this->packageRepo->packageName()} ?? null,
                    'code' => $package?->{$this->packageRepo->packageCode()} ?? null,
                    'package_ids' => $packageIds,
                ];

                $invitation['packages'] = collect($packageIds)->map(function ($id) use ($packagesById) {
                    $pkg = $packagesById->get($id);
                    if ($pkg) {
                        return [
                            'id' => $pkg->{$this->packageRepo->id()},
                            'name' => $pkg->{$this->packageRepo->packageName()},
                            'code' => $pkg->{$this->packageRepo->packageCode()},
                        ];
                    }
                    return null;
                })->filter()->values()->all();

                $relativeLink = (string) ($invitation[$this->invitationRepo->formLink()] ?? '');
                if ($relativeLink) {
                    $invitation[$this->invitationRepo->formLink()] = $baseUrl !== ''
                        ? $baseUrl . '/' . ltrim($relativeLink, '/')
                        : '/' . ltrim($relativeLink, '/');
                }

                return $invitation;
            })
            ->values()
            ->all();

        $result['list'] = $normalized;

        return $result;
    }

    public function createInvitations(array $payload, int $clientId, ?object $user, string $ip, string $userAgent): array
    {
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

        $candidateIdColumn = $this->candidateRepo->id();
        $candidates = $this->candidateRepo->query()
            ->where($this->candidateRepo->clientId(), $clientId)
            ->whereIn($candidateIdColumn, $candidateIds)
            ->get();

        $foundCandidateIds = $candidates->pluck($candidateIdColumn)->map(static fn($id) => (int) $id)->all();
        $invalidCandidateIds = array_values(array_diff($candidateIds, $foundCandidateIds));

        if ($invalidCandidateIds !== []) {
            throw new \Exception('Some candidates are invalid for this client.');
        }

        $packageIdColumn = $this->packageRepo->id();
        $packageClientIdColumn = $this->packageRepo->clientId();
        $packageStatusColumn = $this->packageRepo->status();
        $packageIsActiveColumn = $this->packageRepo->isActive();

        $packages = $this->packageRepo->query()
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
            throw new \Exception('Some packages are invalid or inactive for this client.');
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

        $client = $this->clientRepo->query()
            ->where($this->clientRepo->id(), $clientId)
            ->first();

        $invitationTemplate = $this->emailTemplateRepo->findActiveByCode(
            EmailTemplateCode::CANDIDATE_INVITATION_FORM->value
        );

        $createdInvitations = DB::transaction(function () use ($candidates, $packageIds, $primaryPackageId, $baseFormData, $invitationType, $status, $expiresAt, $now, $user, $ip, $userAgent, $clientId) {
            $invitations = [];

            foreach ($candidates as $candidate) {
                $token = Str::random(64);
                $formLink = '/invitation/' . $token;

                $invitation = $this->invitationRepo->create([
                    $this->invitationRepo->candidateId() => $candidate->{$this->candidateRepo->id()},
                    $this->invitationRepo->clientId() => $clientId,
                    $this->invitationRepo->packageId() => $primaryPackageId,
                    $this->invitationRepo->invitationType() => $invitationType,
                    $this->invitationRepo->invitationToken() => $token,
                    $this->invitationRepo->formLink() => $formLink,
                    $this->invitationRepo->invitedBy() => $user?->id,
                    $this->invitationRepo->invitedAt() => $now,
                    $this->invitationRepo->expiresAt() => $expiresAt,
                    $this->invitationRepo->reminderCount() => 0,
                    $this->invitationRepo->status() => $status,
                ]);

                $this->candidateInvitationsLogRepo->create([
                    $this->candidateInvitationsLogRepo->invitationId() => $invitation->{$this->invitationRepo->id()},
                    $this->candidateInvitationsLogRepo->action() => 'created',
                    $this->candidateInvitationsLogRepo->ipAddress() => $ip,
                    $this->candidateInvitationsLogRepo->userAgent() => $userAgent,
                    $this->candidateInvitationsLogRepo->status() => $status,
                ]);

                $this->candidateRepo->update(
                    $candidate->{$this->candidateRepo->id()},
                    [
                        $this->candidateRepo->status() => CandidateStatus::INVITED->value,
                    ]
                );

                if (!empty($packageIds)) {
                    $candidate->packages()->syncWithoutDetaching($packageIds);
                }

                $invitations[] = $invitation;
            }

            return $invitations;
        });

        if ($invitationTemplate) {
            foreach ($createdInvitations as $invitation) {
                $candidate = $candidates->firstWhere(
                    $this->candidateRepo->id(),
                    $invitation->{$this->invitationRepo->candidateId()}
                );

                $candidateEmail = strtolower(trim((string) ($candidate?->{$this->candidateRepo->email()} ?? '')));
                if ($candidateEmail === '') {
                    continue;
                }

                $candidateFirstName = trim((string) ($candidate?->{$this->candidateRepo->firstName()} ?? ''));
                $candidateLastName = trim((string) ($candidate?->{$this->candidateRepo->lastName()} ?? ''));
                $candidateFullName = trim($candidateFirstName . ' ' . $candidateLastName) ?? $candidateEmail;

                $relativeLink = (string) ($invitation->{$this->invitationRepo->formLink()} ?? '');
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
                    'company_name' => (string) ($client?->{$this->clientRepo->companyName()} ?? config('app.name')),
                    'client_email' => (string) ($client?->{$this->clientRepo->email()} ?? ''),
                    'invitation_token' => (string) ($invitation->{$this->invitationRepo->invitationToken()} ?? ''),
                    'invitation_expires_at' => (string) ($invitation->{$this->invitationRepo->expiresAt()} ?? ''),
                ]);

                $this->emailQueueRepo->create([
                    $this->emailQueueRepo->emailUid() => 'email_' . Str::uuid(),
                    $this->emailQueueRepo->toEmail() => $candidateEmail,
                    $this->emailQueueRepo->toName() => $candidateFullName !== '' ? $candidateFullName : null,
                    $this->emailQueueRepo->subject() => (string) ($rendered['subject'] ?? ''),
                    $this->emailQueueRepo->bodyHtml() => $rendered['body_html'] ?? null,
                    $this->emailQueueRepo->bodyText() => $rendered['body_text'] ?? null,
                    $this->emailQueueRepo->templateId() => $invitationTemplate->{$this->emailTemplateRepo->id()},
                    $this->emailQueueRepo->emailType() => (string) ($invitationTemplate->{$this->emailTemplateRepo->emailType()} ?? 'candidate_invitation'),
                    $this->emailQueueRepo->priority() => (string) ($invitationTemplate->{$this->emailTemplateRepo->defaultPriority()} ?? EmailPriority::NORMAL->value),
                    $this->emailQueueRepo->clientId() => $clientId,
                    $this->emailQueueRepo->candidateId() => $invitation->{$this->invitationRepo->candidateId()},
                    $this->emailQueueRepo->userId() => $user?->id,
                    $this->emailQueueRepo->assignedServerId() => $invitationTemplate->{$this->emailTemplateRepo->serverId()},
                    $this->emailQueueRepo->status() => EmailQueueStatus::PENDING->value,
                    $this->emailQueueRepo->attempts() => 0,
                    $this->emailQueueRepo->maxAttempts() => 3,
                    $this->emailQueueRepo->scheduledAt() => now(),
                    $this->emailQueueRepo->expiresAt() => $invitation->{$this->invitationRepo->expiresAt()},
                ]);

                $this->candidateRepo->update(
                    $invitation->{$this->invitationRepo->candidateId()},
                    [
                        $this->candidateRepo->status() => CandidateStatus::SENT->value
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
            ->pluck($this->invitationRepo->id())
            ->map(static fn($id) => (int) $id)
            ->values()
            ->all();

        $createdInvitationWithRelations = $this->invitationRepo->query()
            ->whereIn($this->invitationRepo->id(), $createdInvitationIds)
            ->with(['candidate', 'package'])
            ->get();

        $baseUrl = rtrim($clientAppUrl, '/');
        $responseInvitations = $createdInvitationWithRelations
            ->map(function ($invitation) use ($packageIds, $baseUrl) {
                $relativeLink = (string) ($invitation->{$this->invitationRepo->formLink()} ?? '');
                $fullFormLink = $baseUrl !== '' && $relativeLink
                    ? $baseUrl . '/' . ltrim($relativeLink, '/')
                    : ($relativeLink ? '/' . ltrim($relativeLink, '/') : null);

                return [
                    'id' => $invitation->{$this->invitationRepo->id()},
                    'candidate_id' => $invitation->{$this->invitationRepo->candidateId()},
                    'package_id' => $invitation->{$this->invitationRepo->packageId()},
                    'package_ids' => $packageIds,
                    'invitation_type' => $invitation->{$this->invitationRepo->invitationType()},
                    'invitation_token' => $invitation->{$this->invitationRepo->invitationToken()},
                    'form_link' => $fullFormLink,
                    'status' => $invitation->{$this->invitationRepo->status()},
                    'invited_at' => $invitation->{$this->invitationRepo->invitedAt()},
                    'expires_at' => $invitation->{$this->invitationRepo->expiresAt()},
                ];
            })
            ->values()
            ->all();

        $responseCandidates = $createdInvitationWithRelations
            ->pluck('candidate')
            ->filter()
            ->unique($this->candidateRepo->id())
            ->map(function ($candidate) {
                return [
                    'id' => $candidate->{$this->candidateRepo->id()},
                    'first_name' => $candidate->{$this->candidateRepo->firstName()},
                    'last_name' => $candidate->{$this->candidateRepo->lastName()},
                    'email' => $candidate->{$this->candidateRepo->email()},
                    'phone' => $candidate->{$this->candidateRepo->phone()},
                    'status' => $candidate->{$this->candidateRepo->status()},
                ];
            })
            ->values()
            ->all();

        $responsePackages = $packages
            ->map(function ($package) {
                return [
                    'id' => $package->{$this->packageRepo->id()},
                    'package_name' => $package->{$this->packageRepo->packageName()},
                    'package_code' => $package->{$this->packageRepo->packageCode()},
                    'type' => $package->{$this->packageRepo->type()},
                    'status' => $package->{$this->packageRepo->status()},
                ];
            })
            ->values()
            ->all();

        return [
            'candidate_ids' => $candidateIds,
            'package_ids' => $packageIds,
            'total_candidates' => count($candidateIds),
            'total_packages' => count($packageIds),
            'total_invitations' => count($responseInvitations),
            'invitations' => $responseInvitations,
            'candidates' => $responseCandidates,
            'packages' => $responsePackages,
        ];
    }

    public function getInvitationByToken(string $token): array
    {
        $invitation = $this->invitationRepo->query()
            ->where($this->invitationRepo->invitationToken(), $token)
            ->with(['candidate', 'package'])
            ->first();

        if (!$invitation) {
            throw new \Exception('Invitation not found or token is invalid.', 404);
        }

        if ((string) ($invitation->{$this->invitationRepo->status()} ?? '') === CandidateInvitationStatus::COMPLETED->value) {
            throw new \Exception('Invitation has already been completed.', 422);
        }

        if ((string) ($invitation->{$this->invitationRepo->status()} ?? '') !== CandidateInvitationStatus::PENDING->value) {
            throw new \Exception('Invitation is not valid.', 422);
        }

        $expiresAt = $invitation->{$this->invitationRepo->expiresAt()};
        if (!empty($expiresAt) && now()->greaterThan($expiresAt)) {
            throw new \Exception('Invitation has expired.', 410);
        }

        [$packageIds, $serviceIds, $serviceFieldsByServiceId, $serviceMetaByServiceId] = $this->buildInvitationContext($invitation);
        $candidate = $invitation->candidate;
        $fieldValuesByFieldId = $this->getCandidateFieldValuesByFieldId(
            (int) ($candidate?->{$this->candidateRepo->id()} ?? 0),
            $serviceIds
        );

        $services = collect();
        if ($serviceIds->isNotEmpty()) {
            $services = $this->serviceRepo->query()
                ->whereIn($this->serviceRepo->id(), $serviceIds->all())
                ->where(function ($query) {
                    $query->where($this->serviceRepo->status(), 'active')
                        ->orWhere($this->serviceRepo->status(), 1);
                })
                ->get()
                ->map(function ($service) use ($serviceFieldsByServiceId, $serviceMetaByServiceId, $fieldValuesByFieldId) {
                    $serviceId = (int) ($service->{$this->serviceRepo->id()} ?? 0);
                    $fields = collect($serviceFieldsByServiceId->get($serviceId, []))
                        ->map(function ($field) use ($fieldValuesByFieldId) {
                            return [
                                'id' => $field->{$this->servicesFieldRepo->id()},
                                'service_id' => $field->{$this->servicesFieldRepo->serviceId()},
                                'field_name' => $field->{$this->servicesFieldRepo->fieldName()},
                                'field_label' => $field->{$this->servicesFieldRepo->fieldLabel()},
                                'section' => $field->section,
                                'field_type' => $field->{$this->servicesFieldRepo->fieldType()},
                                'is_required' => $field->{$this->servicesFieldRepo->isRequired()},
                                'or_group_name' => $field->or_group_name,
                                'validation_regex' => $field->{$this->servicesFieldRepo->validationRegex()},
                                'display_order' => $field->{$this->servicesFieldRepo->displayOrder()},
                                'status' => $field->{$this->servicesFieldRepo->status()},
                                'value' => $fieldValuesByFieldId->get((int) ($field->{$this->servicesFieldRepo->id()} ?? 0)),
                            ];
                        })
                        ->values()
                        ->all();

                    return array_merge([
                        'id' => $service->{$this->serviceRepo->id()},
                        'service_name' => $service->{$this->serviceRepo->serviceName()},
                        'service_code' => $service->{$this->serviceRepo->serviceCode()},
                        'service_category' => $service->{$this->serviceRepo->serviceCategory()},
                        'description' => $service->{$this->serviceRepo->description()},
                        'base_price' => $service->{$this->serviceRepo->basePrice()},
                        'status' => $service->{$this->serviceRepo->status()},
                        'fields' => $fields,
                    ], $serviceMetaByServiceId->get($serviceId, []));
                })
                ->sortBy('display_order')
                ->values();
        }

        $invitationData = $invitation->toArray();

        $clientAppUrl = $this->configurationService->getStringValue(
            ConfigurationKey::CLIENT_APP_URL,
            (string) config('app.client_url', env('CLIENT_URL', ''))
        );
        $baseUrl = rtrim($clientAppUrl, '/');
        $relativeLink = (string) ($invitation->{$this->invitationRepo->formLink()} ?? '');
        $invitationData[$this->invitationRepo->formLink()] = $baseUrl !== '' && $relativeLink
            ? $baseUrl . '/' . ltrim($relativeLink, '/')
            : ($relativeLink ? '/' . ltrim($relativeLink, '/') : null);

        $invitationData['package_ids'] = $packageIds->values()->all();
        $candidateData = $candidate?->toArray();

        $invitationData['candidate'] = $candidateData;
        $invitationData['fields'] = $services->pluck('fields')->flatten(1)->values()->all();
        $invitationData['services'] = $services->values()->all();

        return $invitationData;
    }

    public function updateInvitationByToken(string $token, array $payload, string $ip, string $userAgent): void
    {
        $invitation = $this->invitationRepo->query()
            ->where($this->invitationRepo->invitationToken(), $token)
            ->with(['candidate'])
            ->first();

        if (!$invitation) {
            throw new \Exception('Invitation not found or token is invalid.', 404);
        }

        if ((string) ($invitation->{$this->invitationRepo->status()} ?? '') === CandidateInvitationStatus::COMPLETED->value) {
            throw new \Exception('Invitation has already been completed.', 422);
        }

        if ((string) ($invitation->{$this->invitationRepo->status()} ?? '') !== CandidateInvitationStatus::PENDING->value) {
            throw new \Exception('Invitation is not valid.', 422);
        }

        $expiresAt = $invitation->{$this->invitationRepo->expiresAt()};
        if (!empty($expiresAt) && now()->greaterThan($expiresAt)) {
            throw new \Exception('Invitation has expired.', 410);
        }

        [$formData, $packageIds, $packageServices, $serviceIds, $serviceFieldsByServiceId] = $this->buildInvitationContext($invitation);
        $allowedFields = $serviceFieldsByServiceId
            ->flatten(1)
            ->keyBy($this->servicesFieldRepo->id());

        $inputFields = collect($payload['fields'] ?? []);
        $invalidFieldIds = $inputFields
            ->pluck('field_id')
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->filter(fn($id) => !$allowedFields->has($id))
            ->unique()
            ->values()
            ->all();

        if ($invalidFieldIds !== []) {
            throw new \Exception('Some fields are invalid for this invitation.');
        }

        $candidate = $invitation->candidate;
        if (!$candidate) {
            throw new \Exception('Candidate not found for this invitation.', 404);
        }

        $candidateId = (int) ($candidate->{$this->candidateRepo->id()} ?? 0);
        if ($candidateId <= 0) {
            throw new \Exception('Candidate not found for this invitation.', 404);
        }

        DB::transaction(function () use ($payload, $candidateId, $serviceIds, $allowedFields, $invitation, $ip, $userAgent) {
            $candidateDetails = $payload['candidate_details'] ?? [];

            $this->candidateRepo->update($candidateId, [
                $this->candidateRepo->firstName() => trim((string) ($candidateDetails['first_name'] ?? '')),
                $this->candidateRepo->lastName() => trim((string) ($candidateDetails['last_name'] ?? '')),
                $this->candidateRepo->email() => strtolower(trim((string) ($candidateDetails['email'] ?? ''))),
                $this->candidateRepo->phone() => trim((string) ($candidateDetails['phone'] ?? '')),
                $this->candidateRepo->address() => trim((string) ($candidateDetails['address'] ?? '')),
                $this->candidateRepo->countryId() => !empty($candidateDetails['country_id']) ? (int) $candidateDetails['country_id'] : null,
                $this->candidateRepo->stateId() => !empty($candidateDetails['state_id']) ? (int) $candidateDetails['state_id'] : null,
                $this->candidateRepo->cityId() => !empty($candidateDetails['city_id']) ? (int) $candidateDetails['city_id'] : null,
                $this->candidateRepo->pincode() => !empty($candidateDetails['pincode']) ? (string) $candidateDetails['pincode'] : null,
                $this->candidateRepo->status() => CandidateStatus::ACTIVE->value
            ]);

            $orderItems = collect();
            if ($serviceIds->isNotEmpty()) {
                $orderItems = $this->orderItemRepo->query()
                    ->join('order_candidates', 'order_items.order_candidate_id', '=', 'order_candidates.id')
                    ->where('order_candidates.candidate_id', $candidateId)
                    ->whereIn('order_items.service_id', $serviceIds->all())
                    ->select('order_items.*')
                    ->get()
                    ->keyBy('service_id');
            }

            foreach (($payload['fields'] ?? []) as $fieldInput) {
                $fieldId = (int) ($fieldInput['field_id'] ?? 0);
                if ($fieldId <= 0) {
                    continue;
                }

                $field = $allowedFields->get($fieldId);
                if (!$field) {
                    continue;
                }

                $serviceId = (int) ($field->{$this->servicesFieldRepo->serviceId()} ?? 0);
                if ($serviceId <= 0) {
                    continue;
                }

                $orderItem = $orderItems->get($serviceId);
                if (!$orderItem) {
                    continue; // Skip if no order item exists for this service
                }

                $orderItemId = (int) ($orderItem->id ?? 0);
                if ($orderItemId <= 0) {
                    continue;
                }

                $existingValue = $this->candidateServiceDataRepo->query()
                    ->where('order_item_id', $orderItemId)
                    ->where($this->candidateServiceDataRepo->fieldId(), $fieldId)
                    ->first();

                $value = $fieldInput['value'] ?? null;

                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $path = $value->store('candidate_documents', 'local');
                    $normalizedValue = $path;
                } else {
                    $normalizedValue = is_scalar($value) || $value === null
                        ? $value
                        : json_encode($value);
                }

                if ($existingValue) {
                    $this->candidateServiceDataRepo->update(
                        $existingValue->{$this->candidateServiceDataRepo->id()},
                        [
                            $this->candidateServiceDataRepo->fieldValue() => $normalizedValue,
                        ]
                    );
                    continue;
                }

                $this->candidateServiceDataRepo->create([
                    'order_item_id' => $orderItemId,
                    $this->candidateServiceDataRepo->fieldId() => $fieldId,
                    $this->candidateServiceDataRepo->fieldValue() => $normalizedValue
                ]);
            }

            $this->invitationRepo->update(
                $invitation->{$this->invitationRepo->id()},
                [
                    $this->invitationRepo->status() => CandidateInvitationStatus::COMPLETED->value,
                    $this->invitationRepo->completedAt() => now(),
                ]
            );

            $this->candidateInvitationsLogRepo->create([
                $this->candidateInvitationsLogRepo->invitationId() => $invitation->{$this->invitationRepo->id()},
                $this->candidateInvitationsLogRepo->action() => 'completed',
                $this->candidateInvitationsLogRepo->ipAddress() => $ip,
                $this->candidateInvitationsLogRepo->userAgent() => $userAgent,
                $this->candidateInvitationsLogRepo->status() => CandidateInvitationStatus::COMPLETED->value,
            ]);
        });
    }

    public function resendInvitation(int $invitationId, int $clientId, ?object $user, string $ip, string $userAgent): void
    {
        $invitation = $this->invitationRepo->query()
            ->where($this->invitationRepo->id(), $invitationId)
            ->where($this->invitationRepo->clientId(), $clientId)
            ->with(['candidate'])
            ->first();

        if (!$invitation) {
            throw new \Exception('Invitation not found.', 404);
        }

        if ((string) ($invitation->{$this->invitationRepo->status()} ?? '') === CandidateInvitationStatus::COMPLETED->value) {
            throw new \Exception('Invitation has already been completed.', 422);
        }

        $candidate = $invitation->candidate;
        if (!$candidate) {
            throw new \Exception('Candidate not found for this invitation.', 404);
        }

        $client = $this->clientRepo->query()
            ->where($this->clientRepo->id(), $clientId)
            ->first();

        $invitationTemplate = $this->emailTemplateRepo->findActiveByCode(
            EmailTemplateCode::CANDIDATE_INVITATION_FORM->value
        );

        if (!$invitationTemplate) {
            throw new \Exception('Candidate invitation template not found.', 404);
        }

        $candidateEmail = strtolower(trim((string) ($candidate->{$this->candidateRepo->email()} ?? '')));
        if ($candidateEmail === '') {
            throw new \Exception('Candidate does not have an email address.', 422);
        }

        $clientAppUrl = $this->configurationService->getStringValue(
            ConfigurationKey::CLIENT_APP_URL,
            (string) config('app.client_url', env('CLIENT_URL', ''))
        );
        $baseUrl = rtrim($clientAppUrl, '/');
        $relativeLink = (string) ($invitation->{$this->invitationRepo->formLink()} ?? '');
        $inviteLink = $baseUrl !== ''
            ? $baseUrl . '/' . ltrim($relativeLink, '/')
            : '/' . ltrim($relativeLink, '/');

        $candidateFirstName = trim((string) ($candidate->{$this->candidateRepo->firstName()} ?? ''));
        $candidateLastName = trim((string) ($candidate->{$this->candidateRepo->lastName()} ?? ''));
        $candidateFullName = trim($candidateFirstName . ' ' . $candidateLastName) ?? $candidateEmail;

        DB::transaction(function () use ($invitation, $candidate, $client, $invitationTemplate, $candidateEmail, $candidateFullName, $candidateFirstName, $candidateLastName, $inviteLink, $user, $ip, $userAgent, $clientId) {
            $rendered = $this->emailTemplateService->renderTemplate($invitationTemplate, [
                'candidate_full_name' => $candidateFullName,
                'candidate_first_name' => $candidateFirstName,
                'candidate_last_name' => $candidateLastName,
                'candidate_email' => $candidateEmail,
                'candidate_invite_link' => $inviteLink,
                'company_name' => (string) ($client?->{$this->clientRepo->companyName()} ?? config('app.name')),
                'client_email' => (string) ($client?->{$this->clientRepo->email()} ?? ''),
                'invitation_token' => (string) ($invitation->{$this->invitationRepo->invitationToken()} ?? ''),
                'invitation_expires_at' => (string) ($invitation->{$this->invitationRepo->expiresAt()} ?? ''),
            ]);

            $this->emailQueueRepo->create([
                $this->emailQueueRepo->emailUid() => 'email_' . Str::uuid(),
                $this->emailQueueRepo->toEmail() => $candidateEmail,
                $this->emailQueueRepo->toName() => $candidateFullName !== '' ? $candidateFullName : null,
                $this->emailQueueRepo->subject() => (string) ($rendered['subject'] ?? ''),
                $this->emailQueueRepo->bodyHtml() => $rendered['body_html'] ?? null,
                $this->emailQueueRepo->bodyText() => $rendered['body_text'] ?? null,
                $this->emailQueueRepo->templateId() => $invitationTemplate->{$this->emailTemplateRepo->id()},
                $this->emailQueueRepo->emailType() => (string) ($invitationTemplate->{$this->emailTemplateRepo->emailType()} ?? 'candidate_invitation'),
                $this->emailQueueRepo->priority() => (string) ($invitationTemplate->{$this->emailTemplateRepo->defaultPriority()} ?? EmailPriority::NORMAL->value),
                $this->emailQueueRepo->clientId() => $clientId,
                $this->emailQueueRepo->candidateId() => $invitation->{$this->invitationRepo->candidateId()},
                $this->emailQueueRepo->userId() => $user?->id,
                $this->emailQueueRepo->assignedServerId() => $invitationTemplate->{$this->emailTemplateRepo->serverId()},
                $this->emailQueueRepo->status() => EmailQueueStatus::PENDING->value,
                $this->emailQueueRepo->attempts() => 0,
                $this->emailQueueRepo->maxAttempts() => 3,
                $this->emailQueueRepo->scheduledAt() => now(),
                $this->emailQueueRepo->expiresAt() => $invitation->{$this->invitationRepo->expiresAt()},
            ]);

            $this->invitationRepo->update(
                $invitation->{$this->invitationRepo->id()},
                [
                    $this->invitationRepo->reminderCount() => ((int) ($invitation->{$this->invitationRepo->reminderCount()} ?? 0)) + 1,
                    $this->invitationRepo->updatedAt() => now(),
                ]
            );

            $this->candidateInvitationsLogRepo->create([
                $this->candidateInvitationsLogRepo->invitationId() => $invitation->{$this->invitationRepo->id()},
                $this->candidateInvitationsLogRepo->action() => 'resent',
                $this->candidateInvitationsLogRepo->ipAddress() => $ip,
                $this->candidateInvitationsLogRepo->userAgent() => $userAgent,
                $this->candidateInvitationsLogRepo->status() => $invitation->{$this->invitationRepo->status()},
            ]);
        });
    }

    public function parseResume(string $tempPath, string $extension, string $originalName, string $promptCode): array
    {
        try {
            $result = $this->resumeParserService->parseResumeFile($tempPath, $extension, $originalName, $promptCode);

            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'Resume parsing failed', $result['status_code'] ?? 422);
            }

            return $result['data'] ?? [];
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

    protected function buildInvitationContext($invitation): array
    {
        $packageIds = [];
        $primaryPackageId = (int) ($invitation->{$this->invitationRepo->packageId()} ?? 0);
        if ($primaryPackageId > 0) {
            $packageIds = collect([$primaryPackageId]);
        }

        $packageServices = collect();
        $serviceIds = collect();
        if ($packageIds->isNotEmpty()) {
            $packageServices = $this->packageServiceRepo->query()
                ->whereIn($this->packageServiceRepo->packageId(), $packageIds->all())
                ->where(function ($query) {
                    $query->where($this->packageServiceRepo->status(), 'active')
                        ->orWhere($this->packageServiceRepo->status(), 1);
                })
                ->orderBy($this->packageServiceRepo->displayOrder(), 'asc')
                ->get();

            $serviceIds = $packageServices
                ->pluck($this->packageServiceRepo->serviceId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values();
        }

        $serviceFieldsByServiceId = collect();
        if ($serviceIds->isNotEmpty()) {
            $serviceFieldsByServiceId = $this->servicesFieldRepo->query()
                ->whereIn($this->servicesFieldRepo->serviceId(), $serviceIds->all())
                ->where(function ($query) {
                    $query->where($this->servicesFieldRepo->status(), 'active')
                        ->orWhere($this->servicesFieldRepo->status(), 1);
                })
                ->orderBy($this->servicesFieldRepo->displayOrder(), 'asc')
                ->get()
                ->groupBy($this->servicesFieldRepo->serviceId());
        }

        $serviceMetaByServiceId = $packageServices
            ->groupBy($this->packageServiceRepo->serviceId())
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'display_order' => (int) ($first?->{$this->packageServiceRepo->displayOrder()} ?? 0),
                    'is_mandatory' => (bool) ($first?->{$this->packageServiceRepo->isMandatory()} ?? true),
                ];
            });

        return [$packageIds, $serviceIds, $serviceFieldsByServiceId, $serviceMetaByServiceId];
    }

    protected function getCandidateFieldValuesByFieldId(int $candidateId, Collection $serviceIds): Collection
    {
        if ($candidateId <= 0 || $serviceIds->isEmpty()) {
            return collect();
        }

        $orderItemIds = $this->orderItemRepo->query()
            ->join('order_candidates', 'order_items.order_candidate_id', '=', 'order_candidates.id')
            ->where('order_candidates.candidate_id', $candidateId)
            ->whereIn('order_items.service_id', $serviceIds->all())
            ->pluck('order_items.id')
            ->all();

        if ($orderItemIds === []) {
            return collect();
        }

        return $this->candidateServiceDataRepo->query()
            ->whereIn('order_item_id', $orderItemIds)
            ->get()
            ->pluck($this->candidateServiceDataRepo->fieldValue(), $this->candidateServiceDataRepo->fieldId());
    }
}
