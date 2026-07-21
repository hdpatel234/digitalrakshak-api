<?php

namespace App\Repositories;

use App\Models\SocialLoginProvider;

class SocialLoginProviderRepository extends BaseRepository
{
    public function __construct(SocialLoginProvider $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerName()
    {
        return SocialLoginProvider::PROVIDER_NAME;
    }

    public function providerCode()
    {
        return SocialLoginProvider::PROVIDER_CODE;
    }

    public function icon()
    {
        return SocialLoginProvider::ICON;
    }

    public function color()
    {
        return SocialLoginProvider::COLOR;
    }

    public function description()
    {
        return SocialLoginProvider::DESCRIPTION;
    }

    public function clientId()
    {
        return SocialLoginProvider::CLIENT_ID;
    }

    public function clientSecret()
    {
        return SocialLoginProvider::CLIENT_SECRET;
    }

    public function redirectUrl()
    {
        return SocialLoginProvider::REDIRECT_URL;
    }

    public function scopes()
    {
        return SocialLoginProvider::SCOPES;
    }

    public function authParameters()
    {
        return SocialLoginProvider::AUTH_PARAMETERS;
    }

    public function buttonText()
    {
        return SocialLoginProvider::BUTTON_TEXT;
    }

    public function buttonIcon()
    {
        return SocialLoginProvider::BUTTON_ICON;
    }

    public function buttonColor()
    {
        return SocialLoginProvider::BUTTON_COLOR;
    }

    public function displayOrder()
    {
        return SocialLoginProvider::DISPLAY_ORDER;
    }

    public function isEnabled()
    {
        return SocialLoginProvider::IS_ENABLED;
    }

    public function isDefault()
    {
        return SocialLoginProvider::IS_DEFAULT;
    }

    public function totalUsers()
    {
        return SocialLoginProvider::TOTAL_USERS;
    }

    public function totalConnections()
    {
        return SocialLoginProvider::TOTAL_CONNECTIONS;
    }

    public function lastUsedAt()
    {
        return SocialLoginProvider::LAST_USED_AT;
    }

    public function createdBy()
    {
        return SocialLoginProvider::CREATED_BY;
    }

    public function updatedBy()
    {
        return SocialLoginProvider::UPDATED_BY;
    }
    // functions
}
