<?php

namespace App\Services\ApiService\Admin;

use App\Models\ServiceProvider;

class ServiceProviderService
{
    public function getProviders(array $data)
    {
        $query = ServiceProvider::query();

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('provider_name', 'LIKE', "%{$search}%")
                  ->orWhere('provider_code', 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $providers = $query->paginate($perPage);

        return [
            'list' => $providers->items(),
            'pagination' => [
                'total' => $providers->total(),
                'per_page' => $providers->perPage(),
                'current_page' => $providers->currentPage(),
                'last_page' => $providers->lastPage(),
            ],
            'status_list' => [
                ['key' => 'active', 'name' => 'Active'],
                ['key' => 'inactive', 'name' => 'Inactive'],
                ['key' => 'maintenance', 'name' => 'Maintenance'],
                ['key' => 'deprecated', 'name' => 'Deprecated'],
            ]
        ];
    }

    public function showProvider(ServiceProvider $serviceProvider)
    {
        return $serviceProvider;
    }

    public function storeProvider(array $data)
    {
        return ServiceProvider::create($data);
    }

    public function updateProvider(ServiceProvider $serviceProvider, array $data)
    {
        $serviceProvider->update($data);
        return $serviceProvider;
    }

    public function toggleProviderStatus(ServiceProvider $serviceProvider, string $status)
    {
        $serviceProvider->status = $status;
        $serviceProvider->save();
        return $serviceProvider;
    }

    public function deleteProvider(ServiceProvider $serviceProvider)
    {
        $serviceProvider->delete();
        return true;
    }
}
