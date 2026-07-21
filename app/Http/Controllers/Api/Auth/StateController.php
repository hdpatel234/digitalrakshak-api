<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\ApiResponse;
use App\Repositories\StateRepository;
use Illuminate\Http\Request;

class StateController extends BaseController
{
    use ApiResponse;
    protected StateRepository $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function index(Request $request)
    {
        $states = $this->stateRepository->all();
        if (isset($request->country_id) && $request->country_id != null) {
            $states = $this->stateRepository->getByCountry($request->country_id);
        }

        return $this->success('All states fetched successfully.', $states);
    }
}
