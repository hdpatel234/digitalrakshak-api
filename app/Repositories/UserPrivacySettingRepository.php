<?php

namespace App\Repositories;

use App\Models\UserPrivacySetting;

class UserPrivacySettingRepository extends BaseRepository
{
    public function __construct(UserPrivacySetting $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function profileVisibility()
    {
        return $this->model::PROFILE_VISIBILITY;
    }

    public function showEmail()
    {
        return $this->model::SHOW_EMAIL;
    }

    public function showPhone()
    {
        return $this->model::SHOW_PHONE;
    }

    public function showActivity()
    {
        return $this->model::SHOW_ACTIVITY;
    }

    public function allowDataCollection()
    {
        return $this->model::ALLOW_DATA_COLLECTION;
    }

    public function allowMarketingEmails()
    {
        return $this->model::ALLOW_MARKETING_EMAILS;
    }

    public function allowAnalytics()
    {
        return $this->model::ALLOW_ANALYTICS;
    }

    public function cookieConsent()
    {
        return $this->model::COOKIE_CONSENT;
    }

    public function dataRetentionPreference()
    {
        return $this->model::DATA_RETENTION_PREFERENCE;
    }
    // functions
}