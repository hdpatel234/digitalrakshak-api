<?php

namespace App\Http\Controllers\Api\Client\Company;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Company\UpdateCompanyRequest;
use App\Services\ApiService\Client\CompanyService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    use ApiResponse;

    public function __construct(protected CompanyService $companyService) {}

    public function index(Request $request)
    {
        addInfoLog("Client company list request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $companyData = $this->companyService->getCompanyDetails($clientId);

        return $this->success('Company fetched successfully', $companyData);
    }

    public function update(UpdateCompanyRequest $request)
    {
        addInfoLog("Client company update request");
        
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $companyData = $this->companyService->updateCompanyDetails(
            $clientId,
            $request->except(['logo', 'remove_logo']) + ['remove_logo' => $request->remove_logo],
            $request->file('logo')
        );
        
        return $this->success('Company updated successfully', $companyData);
    }
}
