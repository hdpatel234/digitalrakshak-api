<?php

namespace App\Http\Controllers\Api\Client\Company;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\CompanyService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Request;

class CompanyController extends BaseController
{
    use ApiResponse;

    public function __construct(private CompanyService $companyService) {}

    public function index()
    {
        addInfoLog("Company details data request");

        $company = $this->companyService->index();
        return $this->success('Company fetched successfully', $company);
    }
    public function update(Request $request, $id)
    {
        addInfoLog("Company details update request");

        $company = $this->companyService->update($id, $request);
        return $this->success('Company updated successfully', $company);
    }
}
