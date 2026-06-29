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
        return UserPrivacySetting::USER_ID;
    }

    public function profileVisibility()
    {
        return UserPrivacySetting::PROFILE_VISIBILITY;
    }

    public function showEmail()
    {
        return UserPrivacySetting::SHOW_EMAIL;
    }

    public function showPhone()
    {
        return UserPrivacySetting::SHOW_PHONE;
    }

    public function showActivity()
    {
        return UserPrivacySetting::SHOW_ACTIVITY;
    }

    public function allowDataCollection()
    {
        return UserPrivacySetting::ALLOW_DATA_COLLECTION;
    }

    public function allowMarketingEmails()
    {
        return UserPrivacySetting::ALLOW_MARKETING_EMAILS;
    }

    public function allowAnalytics()
    {
        return UserPrivacySetting::ALLOW_ANALYTICS;
    }

    public function cookieConsent()
    {
        return UserPrivacySetting::COOKIE_CONSENT;
    }

    public function dataRetentionPreference()
    {
        return UserPrivacySetting::DATA_RETENTION_PREFERENCE;
    }
    // functions
}