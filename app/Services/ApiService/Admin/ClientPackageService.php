<?php

namespace App\Services\ApiService\Admin;

use App\Models\Package;
use App\Models\PackageService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ClientPackageService
{
    public function getClientPackages(array $data)
    {
        $query = Package::with('client')->where('type', 'client');

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('package_name', 'LIKE', "%{$search}%")
                  ->orWhere('package_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        
        // Handle sorting by client name
        if ($sortBy === 'client_name') {
            $query->join('clients', 'packages.client_id', '=', 'clients.id')
                  ->select('packages.*')
                  ->orderBy('clients.name', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $packages = $query->paginate($perPage);
        
        $packageIds = collect($packages->items())->pluck('id')->toArray();
        
        $packageServices = PackageService::whereIn('package_id', $packageIds)
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
            $data['available_candidates'] = 0; // This can be updated if we need actual logic
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
        $package = Package::where('type', 'client')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        DB::beginTransaction();
        try {
            PackageService::where('package_id', $package->id)->delete();
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
        $package = Package::where('type', 'client')->find($id);

        if (!$package) {
            throw new \Exception('Package not found.', 404);
        }

        $package->status = $package->status === 'active' ? 'inactive' : 'active';
        $package->is_active = $package->status === 'active' ? 1 : 0;
        $package->save();

        return $package;
    }
}
