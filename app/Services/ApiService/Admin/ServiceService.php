<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ServiceRepository;
use App\Models\Service;

class ServiceService
{
    public function __construct(
        protected ServiceRepository $repo
    ) {}

    public function getServices(array $data)
    {
        $query = $this->repo->query();

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where($this->repo->serviceName(), 'LIKE', "%{$search}%")
                  ->orWhere($this->repo->serviceCode(), 'LIKE', "%{$search}%")
                  ->orWhere($this->repo->description(), 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where($this->repo->status(), $data['status']);
        }

        // Category filtering (optional but good to have)
        if (isset($data['category']) && $data['category'] !== 'all') {
            $query->where($this->repo->serviceCategory(), $data['category']);
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
                'total' => $this->repo->count(),
                'active' => $this->repo->query()->where($this->repo->status(), 'active')->count(),
                'inactive' => $this->repo->query()->where($this->repo->status(), 'inactive')->count(),
                'categories' => $this->repo->query()->whereNotNull($this->repo->serviceCategory())->distinct($this->repo->serviceCategory())->count($this->repo->serviceCategory()),
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
        return $this->repo->create($data);
    }

    public function updateService(Service $service, array $data)
    {
        return $this->repo->update($service->{$this->repo->id()}, $data);
    }
}
