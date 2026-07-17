<?php

namespace App\Services\ApiService\Admin;

use App\Models\Service;

class ServiceService
{
    public function getServices(array $data)
    {
        $query = Service::query();

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('service_name', 'LIKE', "%{$search}%")
                  ->orWhere('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        // Category filtering (optional but good to have)
        if (isset($data['category']) && $data['category'] !== 'all') {
            $query->where('service_category', $data['category']);
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $services = $query->with('category')->paginate($perPage);

        $mappedServices = collect($services->items())->map(function ($service) {
            $data = $service->toArray();
            $data['service_category_name'] = $service->category ? $service->category->category_name : null;
            return $data;
        });

        return [
            'list' => $mappedServices,
            'pagination' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
            ],
            'stats' => [
                'total' => Service::count(),
                'active' => Service::where('status', 'active')->count(),
                'inactive' => Service::where('status', 'inactive')->count(),
                'categories' => Service::whereNotNull('service_category')->distinct('service_category')->count('service_category'),
            ]
        ];
    }

    public function showService(Service $service)
    {
        $service->load('category');
        
        $data = $service->toArray();
        $data['service_category_name'] = $service->category ? $service->category->category_name : null;

        return $data;
    }

    public function storeService(array $data)
    {
        return Service::create($data);
    }

    public function updateService(Service $service, array $data)
    {
        $service->update($data);
        return $service;
    }
}
