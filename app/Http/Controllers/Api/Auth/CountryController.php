<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\ApiResponse;
use App\Repositories\CountryRepository;

class CountryController extends BaseController
{
    use ApiResponse;
    protected CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index()
    {
        $countries = $this->countryRepository->all();

        return $this->success('auth.get_countries.response_messages.countries_success', $countries);
    }
}
