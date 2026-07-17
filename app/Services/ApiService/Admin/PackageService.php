<?php

namespace App\Services\ApiService\Admin;

use App\Models\Package;
use App\Models\PackageService as ClientPackageService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageService
{
    public function getPackages(array $data)
    {
        $query = Package::query()->where('type', 'admin');

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

        $packageIds = collect($packages->items())->pluck('id')->toArray();

        $packageServices = ClientPackageService::whereIn('package_id', $packageIds)
            ->whereIn('status', ['active', 1])
            ->get();

        $serviceIds = $packageServices->pluck('service_id')->unique()->toArray();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $servicesByPackageId = [];
        foreach ($packageServices as $ps) {
            $service = $services->get($ps->service_id);
            if ($service) {
                if (!isset($servicesByPackageId[$ps->package_id])) {
                    $servicesByPackageId[$ps->package_id] = [];
                }
                $servicesByPackageId[$ps->package_id][] = [
                    'service_id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_code' => $service->service_code,
                    'base_price' => $service->base_price,
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
        $totalPackages = Package::where('type', 'admin')->count();
        $activePackages = Package::where('type', 'admin')->where('status', 'active')->count();
        $inactivePackages = Package::where('type', 'admin')->where('status', 'inactive')->count();
        $packageCategories = Package::where('type', 'admin')->distinct('type')->count('type');

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
        $services = Service::whereIn('id', $serviceIds)->get();
        if ($services->count() !== count($serviceIds)) {
            throw new \Exception('Some services are invalid.', 422);
        }

        $totalPrice = $services->sum('base_price');

        $packageCode = 'AP-' . strtoupper(Str::random(5));
        while (Package::where('package_code', $packageCode)->exists()) {
            $packageCode = 'AP-' . strtoupper(Str::random(5));
        }

        DB::beginTransaction();
        try {
            $package = Package::create([
                'package_name' => $data['package_name'],
                'package_code' => $packageCode,
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'type' => 'admin',
                'client_id' => 0,
                'is_active' => 1,
                'status' => $data['status'] ?? 'active',
                'created_by' => $userId,
            ]);

            foreach ($serviceIds as $index => $serviceId) {
                ClientPackageService::create([
                    'package_id' => $package->id,
                    'service_id' => $serviceId,
                    'is_mandatory' => 1,
                    'display_order' => $index + 1,
                    'status' => 'active',
                    'created_by' => $userId,
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
        $package = Package::where('type', 'admin')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $packageData = $package->toArray();
        $packageData['price'] = $package->final_price ?? $package->total_price;

        $services = ClientPackageService::where('package_id', $id)
            ->whereIn('status', ['active', 1])
            ->orderBy('display_order', 'asc')
            ->get();

        $packageData['services'] = $services->map(function ($row) {
            return [
                'id' => $row->id,
                'package_id' => $row->package_id,
                'service_id' => $row->service_id,
            ];
        });

        return $packageData;
    }

    public function updatePackage(int $id, array $data, ?int $userId)
    {
        $package = Package::where('type', 'admin')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $serviceIds = array_values(array_unique($data['service_ids']));

        // Calculate total price based on services
        $services = Service::whereIn('id', $serviceIds)->get();
        if ($services->count() !== count($serviceIds)) {
            throw new \Exception('Some services are invalid.', 422);
        }

        $totalPrice = $services->sum('base_price');

        DB::beginTransaction();
        try {
            $package->update([
                'package_name' => $data['package_name'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? $package->icon,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'status' => $data['status'] ?? $package->status,
                'updated_by' => $userId,
            ]);

            ClientPackageService::where('package_id', $package->id)->delete();

            foreach ($serviceIds as $index => $serviceId) {
                ClientPackageService::create([
                    'package_id' => $package->id,
                    'service_id' => $serviceId,
                    'is_mandatory' => 1,
                    'display_order' => $index + 1,
                    'status' => 'active',
                    'created_by' => $package->created_by,
                    'updated_by' => $userId,
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
        $package = Package::where('type', 'admin')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        DB::beginTransaction();
        try {
            ClientPackageService::where('package_id', $package->id)->delete();
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
        $package = Package::where('type', 'admin')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $newStatus = $package->status === 'active' ? 'inactive' : 'active';
        $package->update([
            'status' => $newStatus,
            'is_active' => $newStatus === 'active' ? 1 : 0,
            'updated_by' => $userId,
        ]);

        return $package;
    }
}
