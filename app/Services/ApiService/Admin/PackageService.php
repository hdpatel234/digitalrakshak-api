<?php

namespace App\Services\ApiService\Admin;

use App\Enums\PackageStatus;
use App\Repositories\PackageRepository;
use App\Repositories\PackageServiceRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageService
{
    public function __construct(
        protected PackageRepository $repo,
        protected PackageServiceRepository $packageServiceRepo,
        protected ServiceRepository $serviceRepo
    ) {}

    public function getPackages(array $data)
    {
        $query = $this->repo->getAdminPackagesQuery($data);

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('package_name', 'LIKE', "%{$search}%")
                    ->orWhere('package_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $packages = $query->paginate($perPage);

        $packageIds = collect($packages->items())->pluck($this->repo->id())->toArray();

        $packageServices = $this->packageServiceRepo->getActiveServicesByPackageIds($packageIds);

        $serviceIds = $packageServices->pluck($this->packageServiceRepo->serviceId())->unique()->toArray();
        $services = $this->serviceRepo->getServicesByIds($serviceIds);

        $servicesByPackageId = [];
        foreach ($packageServices as $ps) {
            $service = $services->get($ps->{$this->packageServiceRepo->serviceId()});
            if ($service) {
                if (!isset($servicesByPackageId[$ps->{$this->packageServiceRepo->packageId()}])) {
                    $servicesByPackageId[$ps->{$this->packageServiceRepo->packageId()}] = [];
                }
                $servicesByPackageId[$ps->{$this->packageServiceRepo->packageId()}][] = [
                    'service_id' => $service->{$this->serviceRepo->id()},
                    'service_name' => $service->{$this->serviceRepo->serviceName()},
                    'service_code' => $service->{$this->serviceRepo->serviceCode()},
                    'base_price' => $service->{$this->serviceRepo->basePrice()},
                ];
            }
        }

        $mappedPackages = collect($packages->items())->map(function ($package) use ($servicesByPackageId) {
            $data = $package->toArray();
            $data['services'] = $servicesByPackageId[$package->id] ?? [];
            $data['available_candidates'] = 0;
            return $data;
        });

        // Stats
        $totalPackages = $this->repo->countByType('admin');
        $activePackages = $this->repo->countByTypeAndStatus('admin', PackageStatus::ACTIVE->value);
        $inactivePackages = $this->repo->countByTypeAndStatus('admin', PackageStatus::INACTIVE->value);
        $packageCategories = $this->repo->countDistinctTypes('admin');

        return [
            'list' => $mappedPackages,
            'pagination' => [
                'total' => $packages->total(),
                'per_page' => $packages->perPage(),
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
            ],
            'stats' => [
                'total' => $totalPackages,
                'active' => $activePackages,
                'inactive' => $inactivePackages,
                'categories' => $packageCategories,
            ]
        ];
    }

    public function storePackage(array $data, ?int $userId)
    {
        $serviceIds = array_values(array_unique($data['service_ids']));

        // Calculate total price based on services
        $services = $this->serviceRepo->getServicesByIds($serviceIds);
        if ($services->count() !== count($serviceIds)) {
            throw new \Exception('Some services are invalid.', 422);
        }

        $totalPrice = $services->sum($this->serviceRepo->basePrice());

        $packageCode = 'AP-' . strtoupper(Str::random(5));
        while ($this->repo->codeExists($packageCode)) {
            $packageCode = 'AP-' . strtoupper(Str::random(5));
        }

        DB::beginTransaction();
        try {
            $package = $this->repo->create([
                $this->repo->packageName() => $data['package_name'],
                $this->repo->packageCode() => $packageCode,
                $this->repo->description() => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                $this->repo->totalPrice() => $totalPrice,
                $this->repo->finalPrice() => $totalPrice,
                $this->repo->type() => 'admin',
                $this->repo->clientId() => 0,
                $this->repo->isActive() => 1,
                $this->repo->status() => $data['status'] ?? PackageStatus::ACTIVE->value,
                $this->repo->createdBy() => $userId,
            ]);

            foreach ($serviceIds as $index => $serviceId) {
                $this->packageServiceRepo->create([
                    $this->packageServiceRepo->packageId() => $package->{$this->repo->id()},
                    $this->packageServiceRepo->serviceId() => $serviceId,
                    $this->packageServiceRepo->isMandatory() => 1,
                    $this->packageServiceRepo->displayOrder() => $index + 1,
                    $this->packageServiceRepo->status() => PackageStatus::ACTIVE->value,
                    $this->packageServiceRepo->createdBy() => $userId,
                ]);
            }

            DB::commit();
            return $package;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function showPackage(int $id)
    {
        $package = $this->repo->findAdminPackage($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $packageData = $package->toArray();
        $packageData['price'] = $package->{$this->repo->finalPrice()} ?? $package->{$this->repo->totalPrice()};

        $services = $this->packageServiceRepo->getActiveServicesByPackageId($id);

        $packageData['services'] = $services->map(function ($row) {
            return [
                'id' => $row->{$this->packageServiceRepo->id()},
                'package_id' => $row->{$this->packageServiceRepo->packageId()},
                'service_id' => $row->{$this->packageServiceRepo->serviceId()},
            ];
        });

        return $packageData;
    }

    public function updatePackage(int $id, array $data, ?int $userId)
    {
        $package = $this->repo->findAdminPackage($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $serviceIds = array_values(array_unique($data['service_ids']));

        // Calculate total price based on services
        $services = $this->serviceRepo->getServicesByIds($serviceIds);
        if ($services->count() !== count($serviceIds)) {
            throw new \Exception('Some services are invalid.', 422);
        }

        $totalPrice = $services->sum($this->serviceRepo->basePrice());

        DB::beginTransaction();
        try {
            $package->update([
                $this->repo->packageName() => $data['package_name'],
                $this->repo->description() => $data['description'] ?? null,
                'icon' => $data['icon'] ?? $package->icon,
                $this->repo->totalPrice() => $totalPrice,
                $this->repo->finalPrice() => $totalPrice,
                $this->repo->status() => $data['status'] ?? $package->{$this->repo->status()},
                $this->repo->updatedBy() => $userId,
            ]);

            $this->packageServiceRepo->deleteByPackageId($package->{$this->repo->id()});

            foreach ($serviceIds as $index => $serviceId) {
                $this->packageServiceRepo->create([
                    $this->packageServiceRepo->packageId() => $package->{$this->repo->id()},
                    $this->packageServiceRepo->serviceId() => $serviceId,
                    $this->packageServiceRepo->isMandatory() => 1,
                    $this->packageServiceRepo->displayOrder() => $index + 1,
                    $this->packageServiceRepo->status() => PackageStatus::ACTIVE->value,
                    $this->packageServiceRepo->createdBy() => $package->{$this->repo->createdBy()},
                    $this->packageServiceRepo->updatedBy() => $userId,
                ]);
            }

            DB::commit();
            return $package;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deletePackage(int $id)
    {
        $package = $this->repo->findAdminPackage($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        DB::beginTransaction();
        try {
            $this->packageServiceRepo->deleteByPackageId($package->{$this->repo->id()});
            $package->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function togglePackageStatus(int $id, ?int $userId)
    {
        $package = $this->repo->findAdminPackage($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $newStatus = $package->{$this->repo->status()} === PackageStatus::ACTIVE->value ? PackageStatus::INACTIVE->value : PackageStatus::ACTIVE->value;

        $package->update([
            $this->repo->status() => $newStatus,
            $this->repo->isActive() => $newStatus === PackageStatus::ACTIVE->value ? 1 : 0,
            $this->repo->updatedBy() => $userId,
        ]);

        return $package;
    }
}
