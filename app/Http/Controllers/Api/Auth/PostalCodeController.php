<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\ApiResponse;
use App\Repositories\PostalCodeRepository;
use Illuminate\Http\Request;

class PostalCodeController extends BaseController
{
    use ApiResponse;
    protected PostalCodeRepository $postalCodeRepository;

    public function __construct(PostalCodeRepository $postalCodeRepository)
    {
        $this->postalCodeRepository = $postalCodeRepository;
    }

    public function index(Request $request)
    {
        $postalCode = $request->query('postal_code');
        
        if (!$postalCode) {
            return $this->error('auth.get_postal_codes.response_messages.postal_code_required', 400);
        }

        // Search by postal code
        $result = $this->postalCodeRepository->query()->where('postal_code', $postalCode)->first();

        if (!$result) {
            return $this->error('auth.get_postal_codes.response_messages.postal_code_not_found', 404);
        }

        return $this->success('auth.get_postal_codes.response_messages.postal_codes_success', $result);
    }
}
