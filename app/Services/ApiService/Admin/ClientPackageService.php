<?php

namespace App\Services\ApiService\Admin;

use App\Enums\PackageStatus;
use App\Repositories\PackageRepository;
use App\Repositories\PackageServiceRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\DB;

class ClientPackageService
{
    public function __construct(
        protected PackageRepository $repo,
        protected PackageServiceRepository $packageServiceRepo,
        protected ServiceRepository $serviceRepo,
    ) {}

    public function getClientPackages(array $data)
    {
        $query = $this->repo->getClientPackagesQuery($data);

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
            $data['services'] = $servicesByPackageId[$package->{$this->repo->id()}] ?? [];
            $data['available_candidates'] = 0;
            return $data;
        });

        return [
            'list' => $mappedPackages,
            'pagination' => [
                'total' => $packages->total(),
                'per_page' => $packages->perPage(),
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
            ]
        ];
    }

    public function deleteClientPackage(int $id)
    {
        $package = $this->repo->query()->where($this->repo->type(), 'client')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        DB::beginTransaction();
        try {
            $this->packageServiceRepo->query()->where($this->packageServiceRepo->packageId(), $package->{$this->repo->id()})->delete();
            $package->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function toggleClientPackageStatus(int $id)
    {
        $package = $this->repo->query()->where($this->repo->type(), 'client')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $package->{$this->repo->status()} = $package->{$this->repo->status()} === PackageStatus::ACTIVE->value ? PackageStatus::INACTIVE->value : PackageStatus::ACTIVE->value;
        $package->{$this->repo->isActive()} = $package->{$this->repo->status()} === PackageStatus::ACTIVE->value ? 1 : 0;
        $package->save();

        return $package;
    }
}
