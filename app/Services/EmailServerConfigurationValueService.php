<?php

namespace App\Services;

use App\Repositories\EmailServerConfigurationValueRepository;

/**
 * @property EmailServerConfigurationValueRepository $repository
 */
class EmailServerConfigurationValueService extends BaseService
{
    public function __construct(EmailServerConfigurationValueRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function emailServerId()
    {
        return $this->repository->emailServerId();
    }

    public function configurationFieldId()
    {
        return $this->repository->configurationFieldId();
    }

    public function fieldValue()
    {
        return $this->repository->fieldValue();
    }
    // functions
}
