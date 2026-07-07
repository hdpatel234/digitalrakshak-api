<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiService\Client\CandidateService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CandidateService $candidateService
    ) {}

    public function index(Request $request)
    {
        $result = $this->candidateService->getCandidates(
            $request->all(),
            null
        );

        return $this->success('Candidates fetched successfully.', $result);
    }
}
