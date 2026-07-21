<?php

namespace App\Services;

use App\Repositories\UserPrivacySettingRepository;

/**
 * @property UserPrivacySettingRepository $repository
 */
class UserPrivacySettingService extends BaseService
{
    
    public function __construct(UserPrivacySettingRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function profileVisibility()
    {
        return $this->repository->profileVisibility();
    }

    public function showEmail()
    {
        return $this->repository->showEmail();
    }

    public function showPhone()
    {
        return $this->repository->showPhone();
    }

    public function showActivity()
    {
        return $this->repository->showActivity();
    }

    public function allowDataCollection()
    {
        return $this->repository->allowDataCollection();
    }

    public function allowMarketingEmails()
    {
        return $this->repository->allowMarketingEmails();
    }

    public function allowAnalytics()
    {
        return $this->repository->allowAnalytics();
    }

    public function cookieConsent()
    {
        return $this->repository->cookieConsent();
    }

    public function dataRetentionPreference()
    {
        return $this->repository->dataRetentionPreference();
    }
    // functions
}
