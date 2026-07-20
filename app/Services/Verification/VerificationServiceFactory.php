<?php

namespace App\Services\Verification;

use App\Enums\ServiceCode;
use Exception;

class VerificationServiceFactory
{
    /**
     * Get the specific verification service instance by service code.
     *
     * @param string $serviceCode
     * @return VerificationServiceInterface
     * @throws Exception
     */ 
    public static function make(string $serviceCode): VerificationServiceInterface
    {
        switch ($serviceCode) {
            case ServiceCode::EMP_VER->value:
                return new EmploymentVerificationService();
            case ServiceCode::ID_VERIFY_PAN->value:
                return new PanVerificationService();
            case ServiceCode::ID_VERIFY_AADHAR->value:
                return new AadharVerificationService();
            case ServiceCode::COURT_VERIFY->value:
                return new CourtVerificationService();
            case ServiceCode::ADDRESS_VERIFY_PHYSICAL->value:
                return new PhysicalAddressVerificationService();
            case ServiceCode::ADDRESS_VERIFY_DIGITAL->value:
                return new DigitalAddressVerificationService();
            case ServiceCode::EDU_VERIFY->value:
                return new EducationVerificationService();
            default:
                throw new Exception("Verification service for code {$serviceCode} not implemented.");
        }
    }
}
