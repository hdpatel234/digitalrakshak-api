<?php

namespace App\Services\ApiService\Client;

use App\Enums\CandidateInvitationStatus;
use App\Services\BaseService;
use App\Services\CandidateInvitationService;
use App\Services\PackageService as BasePackageService;
use App\Services\PackageServiceService;
use App\Services\ServiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ServicesField;

class PackageService extends BaseService
{
    public function __construct(
        protected BasePackageService $packageService,
        protected PackageServiceService $packageServiceService,
        protected ServiceService $serviceService,
        protected CandidateInvitationService $candidateInvitationService
    ) {}

    public function getPackages(array $params, int $clientId): array
    {
        $packageTable = $this->packageService->query()->getModel()->getTable();
        $statusColumn = $this->packageService->status();
        $isActiveColumn = $this->packageService->isActive();
        $clientIdColumn = $this->packageService->clientId();
        $packageNameColumn = $this->packageService->packageName();
        $packageCodeColumn = $this->packageService->packageCode();
        $descriptionColumn = $this->packageService->description();
        $typeColumn = $this->packageService->type();
        $totalPriceColumn = $this->packageService->totalPrice();
        $finalPriceColumn = $this->packageService->finalPrice();

        $qualifiedStatusColumn = $packageTable . '.' . $statusColumn;
        $qualifiedIsActiveColumn = $packageTable . '.' . $isActiveColumn;
        $qualifiedClientIdColumn = $packageTable . '.' . $clientIdColumn;

        $query = $this->packageService->query()
            ->where(function ($builder) use ($qualifiedStatusColumn) {
                $builder->where($qualifiedStatusColumn, 'active')
                    ->orWhere($qualifiedStatusColumn, 1);
            })
            ->where(function ($builder) use ($qualifiedIsActiveColumn) {
                $builder->where($qualifiedIsActiveColumn, 'active')
                    ->orWhere($qualifiedIsActiveColumn, 1);
            })
            ->where(function ($builder) use ($qualifiedClientIdColumn, $clientId) {
                $builder->where($qualifiedClientIdColumn, $clientId)
                    ->orWhere($qualifiedClientIdColumn, 0);
            });

        $result = $this->packageService->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $packageTable . '.' . $packageNameColumn,
                    $packageTable . '.' . $packageCodeColumn,
                    $packageTable . '.' . $descriptionColumn,
                    $packageTable . '.' . $typeColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $packageTable . '.' . $this->packageService->createdAt(),
                'allowed_filters' => [
                    'type' => $packageTable . '.' . $typeColumn,
                    'client_id' => $qualifiedClientIdColumn,
                ],
                'allowed_sorts' => [
                    $packageTable . '.' . $this->packageService->id(),
                    $packageTable . '.' . $packageNameColumn,
                    $packageTable . '.' . $packageCodeColumn,
                    $packageTable . '.' . $typeColumn,
                    $packageTable . '.' . $totalPriceColumn,
                    $packageTable . '.' . $finalPriceColumn,
                    $packageTable . '.' . $this->packageService->createdAt(),
                ],
                'default_sort_by' => $packageTable . '.' . $packageNameColumn,
                'default_sort_direction' => 'asc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $packageIds = collect($result['list'] ?? [])
            ->map(static fn($item) => is_array($item) ? $item : $item->toArray())
            ->pluck($this->packageService->id())
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $availableCandidatesByPackageId = collect();
        $servicesByPackageId = collect();
        if ($packageIds !== []) {
            $availableCandidatesByPackageId = DB::table('candidate_packages')
                ->selectRaw('package_id, COUNT(DISTINCT candidate_id) as total')
                ->whereIn('package_id', $packageIds)
                ->groupBy('package_id')
                ->get()
                ->pluck('total', 'package_id');

            $packageServiceRows = $this->packageServiceService->query()
                ->whereIn($this->packageServiceService->packageId(), $packageIds)
                ->where(function ($builder) {
                    $builder->where($this->packageServiceService->status(), 'active')
                        ->orWhere($this->packageServiceService->status(), 1);
                })
                ->orderBy($this->packageServiceService->displayOrder(), 'asc')
                ->get();

            $serviceIds = $packageServiceRows
                ->pluck($this->packageServiceService->serviceId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $servicesById = $serviceIds === []
                ? collect()
                : $this->serviceService->query()
                    ->whereIn($this->serviceService->id(), $serviceIds)
                    ->get()
                    ->keyBy($this->serviceService->id());

            $servicesByPackageId = $packageServiceRows->groupBy($this->packageServiceService->packageId())
                ->map(function ($rows) use ($servicesById) {
                    return $rows->map(function ($row) use ($servicesById) {
                        $serviceId = (int) ($row->{$this->packageServiceService->serviceId()} ?? 0);
                        $service = $servicesById->get($serviceId);
                        $basePrice = $service?->{$this->serviceService->basePrice()};
                        $priceOverride = $row->{$this->packageServiceService->priceOverride()};

                        return [
                            'package_service_id' => $row->{$this->packageServiceService->id()},
                            'package_id' => $row->{$this->packageServiceService->packageId()},
                            'service_id' => $serviceId,
                            'service_name' => $service?->{$this->serviceService->serviceName()},
                            'service_code' => $service?->{$this->serviceService->serviceCode()},
                            'service_category' => $service?->{$this->serviceService->serviceCategory()},
                            'description' => $service?->{$this->serviceService->description()},
                            'base_price' => $basePrice,
                            'price_override' => $priceOverride,
                            'effective_price' => $priceOverride ?? $basePrice,
                            'is_mandatory' => $row->{$this->packageServiceService->isMandatory()},
                            'display_order' => $row->{$this->packageServiceService->displayOrder()},
                            'status' => $row->{$this->packageServiceService->status()},
                            'service_status' => $service?->{$this->serviceService->status()},
                        ];
                    })->values()->all();
                });
        }

        $result['list'] = collect($result['list'])
            ->map(function ($item) use ($finalPriceColumn, $totalPriceColumn, $availableCandidatesByPackageId, $servicesByPackageId) {
                $package = is_array($item) ? $item : $item->toArray();
                $finalPrice = $package[$finalPriceColumn] ?? null;
                $totalPrice = $package[$totalPriceColumn] ?? null;
                $packageId = (int) ($package[$this->packageService->id()] ?? 0);

                $package['price'] = $finalPrice ?? $totalPrice;
                $package['is_discounted'] = $finalPrice !== null && $totalPrice !== null && (float) $finalPrice < (float) $totalPrice;
                $package['available_candidates'] = (int) $availableCandidatesByPackageId->get($packageId, 0);
                $package['services'] = $servicesByPackageId->get($packageId, []);

                return $package;
            })
            ->values()
            ->all();

        return $result;
    }

    public function createPackage(array $payload, int $clientId, ?object $user): array
    {
        $serviceIds = collect($payload['service_ids'] ?? [])
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $selectedServices = $this->serviceService->query()
            ->whereIn($this->serviceService->id(), $serviceIds)
            ->where(function ($query) {
                $query->where($this->serviceService->status(), 'active')
                    ->orWhere($this->serviceService->status(), 1);
            })
            ->get();

        $activeServiceIds = $selectedServices
            ->pluck($this->serviceService->id())
            ->map(static fn($id) => (int) $id)
            ->values()
            ->all();

        $invalidServiceIds = array_values(array_diff($serviceIds, $activeServiceIds));
        if ($invalidServiceIds !== []) {
            throw new \Exception('Some services are invalid or inactive.', 422);
        }

        $totalPrice = (float) $selectedServices
            ->sum(fn($service) => (float) ($service->{$this->serviceService->basePrice()} ?? 0));

        $created = DB::transaction(function () use ($payload, $serviceIds, $clientId, $user, $totalPrice) {
            $package = $this->packageService->create([
                $this->packageService->packageName() => trim((string) ($payload['package_name'] ?? '')),
                $this->packageService->description() => trim((string) ($payload['description'] ??'')),
                $this->packageService->packageCode() => $this->generatePackageCode($clientId),
                $this->packageService->type() => 'client',
                $this->packageService->clientId() => $clientId,
                $this->packageService->totalPrice() => $totalPrice,
                $this->packageService->finalPrice() => $totalPrice,
                $this->packageService->isActive() => 1,
                $this->packageService->status() => 'active',
                $this->packageService->createdBy() => $user?->id,
            ]);

            $rows = [];
            foreach ($serviceIds as $index => $serviceId) {
                $rows[] = $this->packageServiceService->create([
                    $this->packageServiceService->packageId() => $package->{$this->packageService->id()},
                    $this->packageServiceService->serviceId() => $serviceId,
                    $this->packageServiceService->isMandatory() => 1,
                    $this->packageServiceService->displayOrder() => $index + 1,
                    $this->packageServiceService->status() => 'active',
                    $this->packageServiceService->createdBy() => $user?->id,
                ]);
            }

            return [$package, $rows];
        });

        [$package, $packageServiceRows] = $created;

        return [
            'id' => $package->{$this->packageService->id()},
            'package_name' => $package->{$this->packageService->packageName()},
            'package_code' => $package->{$this->packageService->packageCode()},
            'client_id' => $package->{$this->packageService->clientId()},
            'type' => $package->{$this->packageService->type()},
            'total_price' => $package->{$this->packageService->totalPrice()},
            'final_price' => $package->{$this->packageService->finalPrice()},
            'status' => $package->{$this->packageService->status()},
            'service_ids' => collect($packageServiceRows)
                ->pluck($this->packageServiceService->serviceId())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all(),
        ];
    }

    public function getPackage(int $packageId, int $clientId): array
    {
        $packageModel = $this->packageService->query()
            ->where($this->packageService->id(), $packageId)
            ->where(function ($builder) {
                $builder->where($this->packageService->status(), 'active')
                    ->orWhere($this->packageService->status(), 1);
            })
            ->where(function ($builder) {
                $builder->where($this->packageService->isActive(), 'active')
                    ->orWhere($this->packageService->isActive(), 1);
            })
            ->where(function ($builder) use ($clientId) {
                $builder->where($this->packageService->clientId(), $clientId)
                    ->orWhere($this->packageService->clientId(), 0);
            })
            ->first();

        if (!$packageModel) {
            throw new \Exception('Package not found.', 404);
        }

        $packageData = $packageModel->toArray();
        $totalPrice = $packageData[$this->packageService->totalPrice()] ?? null;
        $finalPrice = $packageData[$this->packageService->finalPrice()] ?? null;

        $availableCandidates = (int) $this->candidateInvitationService->query()
            ->where($this->candidateInvitationService->packageId(), (int) ($packageData[$this->packageService->id()] ?? 0))
            ->where($this->candidateInvitationService->status(), CandidateInvitationStatus::COMPLETED->value)
            ->distinct($this->candidateInvitationService->candidateId())
            ->count($this->candidateInvitationService->candidateId());

        $packageData['price'] = $finalPrice ?? $totalPrice;
        $packageData['is_discounted'] = $finalPrice !== null && $totalPrice !== null && (float) $finalPrice < (float) $totalPrice;
        $packageData['available_candidates'] = $availableCandidates;

        return $packageData;
    }

    public function getPackageServices(int $packageId, int $clientId): array
    {
        $packageModel = $this->packageService->query()
            ->where($this->packageService->id(), $packageId)
            ->where(function ($builder) {
                $builder->where($this->packageService->status(), 'active')
                    ->orWhere($this->packageService->status(), 1);
            })
            ->where(function ($builder) {
                $builder->where($this->packageService->isActive(), 'active')
                    ->orWhere($this->packageService->isActive(), 1);
            })
            ->where(function ($builder) use ($clientId) {
                $builder->where($this->packageService->clientId(), $clientId)
                    ->orWhere($this->packageService->clientId(), 0);
            })
            ->first();

        if (!$packageModel) {
            throw new \Exception('Package not found.', 404);
        }

        $packageServiceRows = $this->packageServiceService->query()
            ->where($this->packageServiceService->packageId(), $packageId)
            ->where(function ($builder) {
                $builder->where($this->packageServiceService->status(), 'active')
                    ->orWhere($this->packageServiceService->status(), 1);
            })
            ->orderBy($this->packageServiceService->displayOrder(), 'asc')
            ->get();

        $serviceIds = $packageServiceRows
            ->pluck($this->packageServiceService->serviceId())
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $servicesById = $serviceIds === []
            ? collect()
            : $this->serviceService->query()
                ->whereIn($this->serviceService->id(), $serviceIds)
                ->get()
                ->keyBy($this->serviceService->id());

        $fieldsByServiceId = $serviceIds === []
            ? collect()
            : ServicesField::whereIn(ServicesField::SERVICE_ID, $serviceIds)
                ->where(function ($query) {
                    $query->where(ServicesField::STATUS, 'active')
                        ->orWhere(ServicesField::STATUS, 1);
                })
                ->orderBy(ServicesField::DISPLAY_ORDER, 'asc')
                ->get()
                ->groupBy(ServicesField::SERVICE_ID);

        $services = $packageServiceRows
            ->map(function ($row) use ($servicesById, $fieldsByServiceId) {
                $serviceId = (int) ($row->{$this->packageServiceService->serviceId()} ?? 0);
                $service = $servicesById->get($serviceId);
                $basePrice = $service?->{$this->serviceService->basePrice()};
                $priceOverride = $row->{$this->packageServiceService->priceOverride()};

                $serviceFields = $fieldsByServiceId->get($serviceId, collect())->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'field_name' => $field->field_name,
                        'field_label' => $field->field_label,
                        'section' => $field->section,
                        'field_type' => $field->field_type,
                        'is_required' => $field->is_required,
                        'validation_regex' => $field->validation_regex,
                        'display_order' => $field->display_order,
                    ];
                })->values()->all();

                return [
                    'package_service_id' => $row->{$this->packageServiceService->id()},
                    'package_id' => $row->{$this->packageServiceService->packageId()},
                    'service_id' => $serviceId,
                    'service_name' => $service?->{$this->serviceService->serviceName()},
                    'service_code' => $service?->{$this->serviceService->serviceCode()},
                    'service_category' => $service?->{$this->serviceService->serviceCategory()},
                    'description' => $service?->{$this->serviceService->description()},
                    'base_price' => $basePrice,
                    'price_override' => $priceOverride,
                    'effective_price' => $priceOverride ?? $basePrice,
                    'is_mandatory' => $row->{$this->packageServiceService->isMandatory()},
                    'display_order' => $row->{$this->packageServiceService->displayOrder()},
                    'status' => $row->{$this->packageServiceService->status()},
                    'service_status' => $service?->{$this->serviceService->status()},
                    'fields' => $serviceFields,
                ];
            })
            ->values()
            ->all();

        return [
            'package_id' => $packageModel->{$this->packageService->id()},
            'package_name' => $packageModel->{$this->packageService->packageName()},
            'package_code' => $packageModel->{$this->packageService->packageCode()},
            'services' => $services,
        ];
    }

    public function getPackageCandidates(int $packageId, int $clientId): array
    {
        $packageModel = $this->packageService->query()
            ->where($this->packageService->id(), $packageId)
            ->where(function ($builder) {
                $builder->where($this->packageService->status(), 'active')
                    ->orWhere($this->packageService->status(), 1);
            })
            ->where(function ($builder) {
                $builder->where($this->packageService->isActive(), 'active')
                    ->orWhere($this->packageService->isActive(), 1);
            })
            ->where(function ($builder) use ($clientId) {
                $builder->where($this->packageService->clientId(), $clientId)
                    ->orWhere($this->packageService->clientId(), 0);
            })
            ->first();

        if (!$packageModel) {
            throw new \Exception('Package not found.', 404);
        }

        $candidates = \App\Models\Candidate::whereHas('packages', function ($q) use ($packageId) {
            $q->where('candidate_packages.package_id', $packageId);
        })
        ->where('client_id', $clientId)
        ->orderByDesc('id')
        ->get()
        ->map(function ($candidate) {
            return [
                'candidate_id' => $candidate->id,
                'candidate' => $candidate->toArray(),
            ];
        })
        ->values()
        ->all();

        return [
            'package_id' => (int) $packageModel->{$this->packageService->id()},
            'package_name' => $packageModel->{$this->packageService->packageName()},
            'package_code' => $packageModel->{$this->packageService->packageCode()},
            'total_candidates' => count($candidates),
            'candidates' => $candidates,
        ];
    }

    protected function generatePackageCode(int $clientId): string
    {
        do {
            $code = 'CP-' . $clientId . '-' . Str::upper(Str::random(5));
            $exists = $this->packageService->query()
                ->where($this->packageService->packageCode(), $code)
                ->exists();
        } while ($exists);

        return $code;
    }
}
