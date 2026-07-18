<?php

namespace App\Enums;

enum ServiceFieldName: string
{
    case BENEFICIARY_ACCOUNT = 'beneficiary_account';
    case BENEFICIARY_IFSC = 'beneficiary_ifsc';
    case UAN = 'uan';
}
