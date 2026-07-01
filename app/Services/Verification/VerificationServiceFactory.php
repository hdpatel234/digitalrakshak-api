<?php

namespace App\Services\Verification;

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
            case 'EMP_VER':
                return new EmploymentVerificationService();
            case 'ID_VERIFY_PAN':
                return new PanVerificationService();
            case 'ID_VERIFY_AADHAR':
                return new AadharVerificationService();
            case 'COURT_VERIFY':
                return new CourtVerificationService();
            case 'ADDRESS_VERIFY_PHYSICAL':
                return new PhysicalAddressVerificationService();
            case 'ADDRESS_VERIFY_DIGITAL':
                return new DigitalAddressVerificationService();
            case 'EDU_VERIFY':
                return new EducationVerificationService();
            default:
                throw new Exception("Verification service for code {$serviceCode} not implemented.");
        }
    }
}
