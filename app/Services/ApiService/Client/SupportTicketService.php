<?php

namespace App\Services\ApiService\Client;

use App\Repositories\SupportDepartmentRepository;
use App\Repositories\SupportPriorityRepository;
use App\Repositories\SupportTicketRepository;
use App\Services\BaseService;

class SupportTicketService extends BaseService
{
    protected SupportDepartmentRepository $departmentRepository;
    protected SupportPriorityRepository $priorityRepository;

    public function __construct(SupportTicketRepository $repository, SupportDepartmentRepository $departmentRepository, SupportPriorityRepository $priorityRepository)
    {
        $this->departmentRepository = $departmentRepository;
        $this->priorityRepository = $priorityRepository;
        parent::__construct($repository);
    }

    public function getDepartments()
    {
        return $this->departmentRepository->getActiveDepartments();
    }

    public function getPriorities()
    {
        return $this->priorityRepository->getActivePriorities();
    }
}
