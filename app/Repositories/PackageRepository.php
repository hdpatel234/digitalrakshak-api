<?php

namespace App\Repositories;

use App\Models\Package;

class PackageRepository extends BaseRepository
{
    public function __construct(Package $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function packageName()
    {
        return Package::PACKAGE_NAME;
    }

    public function packageCode()
    {
        return Package::PACKAGE_CODE;
    }

    public function description()
    {
        return Package::DESCRIPTION;
    }

    public function type()
    {
        return Package::TYPE;
    }

    public function clientId()
    {
        return Package::CLIENT_ID;
    }

    public function totalPrice()
    {
        return Package::TOTAL_PRICE;
    }

    public function discountType()
    {
        return Package::DISCOUNT_TYPE;
    }

    public function discountValue()
    {
        return Package::DISCOUNT_VALUE;
    }

    public function finalPrice()
    {
        return Package::FINAL_PRICE;
    }

    // functions
    public function getClientPackagesQuery(array $data)
    {
        $query = $this->query()->with('client')->where($this->type(), 'client');

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where($this->packageName(), 'LIKE', "%{$search}%")
                    ->orWhere($this->packageCode(), 'LIKE', "%{$search}%")
                    ->orWhere($this->description(), 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('company_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? $this->createdAt();
        $sortDirection = $data['sort_direction'] ?? 'desc';

        // Handle sorting by client name
        if ($sortBy === 'client_name') {
            $query->join('clients', $this->model->getTable() . '.' . $this->clientId(), '=', 'clients.id')
                ->select($this->model->getTable() . '.*')
                ->orderBy('clients.name', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query;
    }

    public function getAdminPackagesQuery(array $data)
    {
        $query = $this->query()->where($this->type(), 'admin');

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where($this->packageName(), 'LIKE', "%{$search}%")
                    ->orWhere($this->packageCode(), 'LIKE', "%{$search}%")
                    ->orWhere($this->description(), 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? $this->createdAt();
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query;
    }

    public function countByType(string $type)
    {
        return $this->query()->where($this->type(), $type)->count();
    }

    public function countByTypeAndStatus(string $type, string $status)
    {
        return $this->query()->where($this->type(), $type)->where($this->status(), $status)->count();
    }

    public function countDistinctTypes(string $type)
    {
        return $this->query()->where($this->type(), $type)->distinct($this->type())->count($this->type());
    }

    public function codeExists(string $code)
    {
        return $this->query()->where($this->packageCode(), $code)->exists();
    }

    public function findAdminPackage(int $id)
    {
        return $this->query()->where($this->type(), 'admin')->find($id);
    }
}
