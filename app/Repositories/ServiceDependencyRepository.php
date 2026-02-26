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
        return $this->model::SERVICE_ID;
    }

    public function dependsOnServiceId()
    {
        return $this->model::DEPENDS_ON_SERVICE_ID;
    }

    public function dependencyType()
    {
        return $this->model::DEPENDENCY_TYPE;
    }
    // functions
}