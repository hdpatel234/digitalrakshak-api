<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ServiceProviderRepository;
use App\Models\ServiceProvider;

class ServiceProviderService
{
    public function __construct(
        protected ServiceProviderRepository $repo
    ) {}

    public function getProviders(array $data)
    {
        $query = $this->repo->query();

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where($this->repo->providerName(), 'LIKE', "%{$search}%")
                  ->orWhere($this->repo->providerCode(), 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where($this->repo->status(), $data['status']);
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
        return $this->repo->create($data);
    }

    public function updateProvider(ServiceProvider $serviceProvider, array $data)
    {
        return $this->repo->update($serviceProvider->{$this->repo->id()}, $data);
    }

    public function toggleProviderStatus(ServiceProvider $serviceProvider, string $status)
    {
        return $this->repo->update($serviceProvider->{$this->repo->id()}, [$this->repo->status() => $status]);
    }

    public function deleteProvider(ServiceProvider $serviceProvider)
    {
        $this->repo->delete($serviceProvider->{$this->repo->id()});
        return true;
    }
}
