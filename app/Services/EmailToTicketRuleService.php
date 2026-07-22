<?php

namespace App\Services;

use App\Repositories\EmailToTicketRuleRepository;

/**
 * @property EmailToTicketRuleRepository $repository
 */
class EmailToTicketRuleService extends BaseService
{
    public function __construct(protected EmailToTicketRuleRepository $repository) {}

    // column constants
    public function ruleName()
    {
        return $this->repository->ruleName();
    }

    public function rulePriority()
    {
        return $this->repository->rulePriority();
    }

    public function matchType()
    {
        return $this->repository->matchType();
    }

    public function matchValue()
    {
        return $this->repository->matchValue();
    }

    public function matchPattern()
    {
        return $this->repository->matchPattern();
    }

    public function ticketDepartmentId()
    {
        return $this->repository->ticketDepartmentId();
    }

    public function ticketPriorityId()
    {
        return $this->repository->ticketPriorityId();
    }

    public function ticketCategory()
    {
        return $this->repository->ticketCategory();
    }

    public function autoAssignUserId()
    {
        return $this->repository->autoAssignUserId();
    }

    public function autoResponseTemplateId()
    {
        return $this->repository->autoResponseTemplateId();
    }

    public function createTicket()
    {
        return $this->repository->createTicket();
    }

    public function sendAutoResponse()
    {
        return $this->repository->sendAutoResponse();
    }

    public function ticketSubjectPrefix()
    {
        return $this->repository->ticketSubjectPrefix();
    }

    public function ticketSubjectSuffix()
    {
        return $this->repository->ticketSubjectSuffix();
    }

    public function customerEmailField()
    {
        return $this->repository->customerEmailField();
    }

    public function customerNameField()
    {
        return $this->repository->customerNameField();
    }

    public function escalateAfterHours()
    {
        return $this->repository->escalateAfterHours();
    }

    public function escalateUserId()
    {
        return $this->repository->escalateUserId();
    }

    public function additionalConfig()
    {
        return $this->repository->additionalConfig();
    }

    // functions
}
