<?php

namespace App\Http\Controllers\Api\Client\Company;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\CompanyService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    use ApiResponse;

    public function __construct(private CompanyService $companyService) {}

    public function index()
    {
        addInfoLog("Company details data request");

        $company = $this->companyService->index();
        
        $companyData = $company->toArray();
        if (!empty($companyData['logo'])) {
            $companyData['logo'] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $companyData['logo'], '/');
        }

        return $this->success('Company fetched successfully', $companyData);
    }
    public function update(Request $request)
    {
        addInfoLog("Company details update request");
        
        $id = $request->user()->client_id;
        $data = $request->except(['logo', 'remove_logo']);
        
        if ($request->has('remove_logo') && $request->remove_logo === 'true') {
            $data['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company_logos', 'public');
            $data['logo'] = $path;
        }

        $company = $this->companyService->update($id, $data);
        
        $companyData = $company->toArray();
        if (!empty($companyData['logo'])) {
            $companyData['logo'] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $companyData['logo'], '/');
        }
        
        return $this->success('Company updated successfully', $companyData);
    }
}
