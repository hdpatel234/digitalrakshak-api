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
        return $this->model::PROVIDER_NAME;
    }

    public function providerCode()
    {
        return $this->model::PROVIDER_CODE;
    }

    public function icon()
    {
        return $this->model::ICON;
    }

    public function color()
    {
        return $this->model::COLOR;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function clientSecret()
    {
        return $this->model::CLIENT_SECRET;
    }

    public function redirectUrl()
    {
        return $this->model::REDIRECT_URL;
    }

    public function scopes()
    {
        return $this->model::SCOPES;
    }

    public function authParameters()
    {
        return $this->model::AUTH_PARAMETERS;
    }

    public function buttonText()
    {
        return $this->model::BUTTON_TEXT;
    }

    public function buttonIcon()
    {
        return $this->model::BUTTON_ICON;
    }

    public function buttonColor()
    {
        return $this->model::BUTTON_COLOR;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function isEnabled()
    {
        return $this->model::IS_ENABLED;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function totalUsers()
    {
        return $this->model::TOTAL_USERS;
    }

    public function totalConnections()
    {
        return $this->model::TOTAL_CONNECTIONS;
    }

    public function lastUsedAt()
    {
        return $this->model::LAST_USED_AT;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}