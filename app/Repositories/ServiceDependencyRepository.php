<?php

namespace App\Repositories;

use App\Models\ServiceDependency;

class ServiceDependencyRepository extends BaseRepository
{
    public function __construct(ServiceDependency $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceId()
    {
        return ServiceDependency::SERVICE_ID;
    }

    public function dependsOnServiceId()
    {
        return ServiceDependency::DEPENDS_ON_SERVICE_ID;
    }

    public function dependencyType()
    {
        return ServiceDependency::DEPENDENCY_TYPE;
    }
    // functions
}
