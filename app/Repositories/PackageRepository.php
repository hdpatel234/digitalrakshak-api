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

    public function isActive()
    {
        return Package::IS_ACTIVE;
    }

    public function status()
    {
        return Package::STATUS;
    }

    public function createdBy()
    {
        return Package::CREATED_BY;
    }

    public function updatedBy()
    {
        return Package::UPDATED_BY;
    }

    public function deletedBy()
    {
        return Package::DELETED_BY;
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
                  ->orWhereHas('client', function($q) use ($search) {
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
}