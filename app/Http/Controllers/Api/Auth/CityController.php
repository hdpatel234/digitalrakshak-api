<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\ApiResponse;
use App\Repositories\CityRepository;
use Illuminate\Http\Request;

class CityController extends BaseController
{
    use ApiResponse;
    protected CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function index(Request $request)
    {
        $cities = $this->cityRepository->all();
        if (isset($reqest->state_id) && $request->state_id != null) {
            $cities = $this->cityRepository->getByState($request->state_id);
        }

        return $this->success('All cities fetched successfully.', $cities);
    }
}