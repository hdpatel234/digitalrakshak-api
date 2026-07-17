<?php

namespace App\Repositories;

use App\Models\PackageService;

class PackageServiceRepository extends BaseRepository
{
    public function __construct(PackageService $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function packageId()
    {
        return PackageService::PACKAGE_ID;
    }

    public function serviceId()
    {
        return PackageService::SERVICE_ID;
    }

    public function priceOverride()
    {
        return PackageService::PRICE_OVERRIDE;
    }

    public function isMandatory()
    {
        return PackageService::IS_MANDATORY;
    }

    public function displayOrder()
    {
        return PackageService::DISPLAY_ORDER;
    }

    public function status()
    {
        return PackageService::STATUS;
    }

    public function createdBy()
    {
        return PackageService::CREATED_BY;
    }

    public function updatedBy()
    {
        return PackageService::UPDATED_BY;
    }

    public function deletedBy()
    {
        return PackageService::DELETED_BY;
    }

    // functions
    public function getActiveServicesByPackageIds(array $packageIds)
    {
        return $this->query()
            ->whereIn($this->packageId(), $packageIds)
            ->whereIn($this->status(), ['active', 1])
            ->get();
    }
}