<?php
namespace App\Http\Controllers\Api\Client\Package;

use App\Enums\CandidateInvitationStatus;
use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Package\StorePackageRequest;
use App\Services\CandidateInvitationService;
use App\Services\PackageService;
use App\Services\PackageServiceService;
use App\Services\ServiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageController extends BaseController
{
    use ApiResponse;
    protected PackageService $service;
    protected PackageServiceService $packageServiceService;
    protected ServiceService $serviceService;
    protected CandidateInvitationService $candidateInvitationService;

    public function __construct(
        PackageService $service,
        PackageServiceService $packageServiceService,
        ServiceService $serviceService,
        CandidateInvitationService $candidateInvitationService
    )
    {
        $this->service = $service;
        $this->packageServiceService = $packageServiceService;
        $this->serviceService = $serviceService;
        $this->candidateInvitationService = $candidateInvitationService;
    }

    public function index(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $packageTable = $this->service->query()->getModel()->getTable();
        $statusColumn = $this->service->status();
        $isActiveColumn = $this->service->isActive();
        $clientIdColumn = $this->service->clientId();
        $packageNameColumn = $this->service->packageName();
        $packageCodeColumn = $this->service->packageCode();
        $descriptionColumn = $this->service->description();
        $typeColumn = $this->service->type();
        $totalPriceColumn = $this->service->totalPrice();
        $finalPriceColumn = $this->service->finalPrice();

        $qualifiedStatusColumn = $packageTable . '.' . $statusColumn;
        $qualifiedIsActiveColumn = $packageTable . '.' . $isActiveColumn;
        $qualifiedClientIdColumn = $packageTable . '.' . $clientIdColumn;

        $query = $this->service->query()
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

        $result = $this->service->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
                    $packageTable . '.' . $packageNameColumn,
                    $packageTable . '.' . $packageCodeColumn,
                    $packageTable . '.' . $descriptionColumn,
                    $packageTable . '.' . $typeColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $packageTable . '.' . $this->service->createdAt(),
                'allowed_filters' => [
                    'type' => $packageTable . '.' . $typeColumn,
                    'client_id' => $qualifiedClientIdColumn,
                ],
                'allowed_sorts' => [
                    $packageTable . '.' . $this->service->id(),
                    $packageTable . '.' . $packageNameColumn,
                    $packageTable . '.' . $packageCodeColumn,
                    $packageTable . '.' . $typeColumn,
                    $packageTable . '.' . $totalPriceColumn,
                    $packageTable . '.' . $finalPriceColumn,
                    $packageTable . '.' . $this->service->createdAt(),
                ],
                'default_sort_by' => $packageTable . '.' . $packageNameColumn,
                'default_sort_direction' => 'asc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $packageIds = collect($result['list'] ?? [])
            ->map(static fn($item) => is_array($item) ? $item : $item->toArray())
            ->pluck($this->service->id())
            ->map(static fn($id) => (int) $id)
            ->filter(static fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $availableCandidatesByPackageId = collect();
        if ($packageIds !== []) {
            $availableCandidatesByPackageId = $this->candidateInvitationService->query()
                ->selectRaw(
                    $this->candidateInvitationService->packageId() . ', COUNT(DISTINCT ' . $this->candidateInvitationService->candidateId() . ') as total'
                )
                ->whereIn($this->candidateInvitationService->packageId(), $packageIds)
                ->where($this->candidateInvitationService->status(), CandidateInvitationStatus::COMPLETED->value)
                ->groupBy($this->candidateInvitationService->packageId())
                ->get()
                ->pluck('total', $this->candidateInvitationService->packageId());
        }

        $result['list'] = collect($result['list'])
            ->map(function ($item) use ($finalPriceColumn, $totalPriceColumn, $availableCandidatesByPackageId) {
                $package = is_array($item) ? $item : $item->toArray();
                $finalPrice = $package[$finalPriceColumn] ?? null;
                $totalPrice = $package[$totalPriceColumn] ?? null;

                $package['price'] = $finalPrice ?? $totalPrice;
                $package['is_discounted'] = $finalPrice !== null && $totalPrice !== null && (float) $finalPrice < (float) $totalPrice;
                $package['available_candidates'] = (int) $availableCandidatesByPackageId->get((int) ($package[$this->service->id()] ?? 0), 0);

                return $package;
            })
            ->values()
            ->all();

        return $this->success('Packages fetched successfully.', $result);
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $payload = $request->validated();
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
            return $this->validationError([
                'service_ids' => ['Some services are invalid or inactive.'],
                'invalid_service_ids' => $invalidServiceIds,
            ]);
        }

        $totalPrice = (float) $selectedServices
            ->sum(fn($service) => (float) ($service->{$this->serviceService->basePrice()} ?? 0));

        $created = DB::transaction(function () use ($payload, $serviceIds, $clientId, $user, $totalPrice) {
            $package = $this->service->create([
                $this->service->packageName() => trim((string) ($payload['package_name'] ?? '')),
                $this->service->description() => trim((string) ($payload['description'] ??'')),
                $this->service->packageCode() => $this->generatePackageCode($clientId),
                $this->service->type() => 'client',
                $this->service->clientId() => $clientId,
                $this->service->totalPrice() => $totalPrice,
                $this->service->finalPrice() => $totalPrice,
                $this->service->isActive() => 1,
                $this->service->status() => 'active',
                $this->service->createdBy() => $user?->id,
            ]);

            $rows = [];
            foreach ($serviceIds as $index => $serviceId) {
                $rows[] = $this->packageServiceService->create([
                    $this->packageServiceService->packageId() => $package->{$this->service->id()},
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

        return $this->success('Package created successfully.', [
            'id' => $package->{$this->service->id()},
            'package_name' => $package->{$this->service->packageName()},
            'package_code' => $package->{$this->service->packageCode()},
            'client_id' => $package->{$this->service->clientId()},
            'type' => $package->{$this->service->type()},
            'total_price' => $package->{$this->service->totalPrice()},
            'final_price' => $package->{$this->service->finalPrice()},
            'status' => $package->{$this->service->status()},
            'service_ids' => collect($packageServiceRows)
                ->pluck($this->packageServiceService->serviceId())
                ->map(static fn($id) => (int) $id)
                ->values()
                ->all(),
        ], 201);
    }

    protected function generatePackageCode(int $clientId): string
    {
        do {
            $code = 'CP-' . $clientId . '-' . Str::upper(Str::random(5));
            $exists = $this->service->query()
                ->where($this->service->packageCode(), $code)
                ->exists();
        } while ($exists);

        return $code;
    }
}
