<?php

namespace App\Enums;

enum EmailTemplateCode: string
{
    case CANDIDATE_INVITATION_FORM = 'EMAIL-CLIENT-INV-001';
    case CLIENT_ORDER_CONFIRMATION = 'EMAIL-CLIENT-ORD-CONF-001';
}
