<?php

namespace App\Enums;

enum ConfigurationKey: string
{
    case INVITATION_LINK_EXPIRY_DAYS = 'invitation_link_expiry_days';
    case CLIENT_APP_URL = 'client_app_url';
    case ADMIN_APP_URL = 'admin_app_url';
}
