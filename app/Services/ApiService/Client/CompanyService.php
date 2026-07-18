<?php

namespace App\Services\ApiService\Client;

use App\Repositories\ClientRepository;
use App\Services\BaseService;

class CompanyService extends BaseService
{
    public function __construct(
        protected ClientRepository $clientRepository
    ) {}

    public function getCompanyDetails(int $clientId)
    {
        $client = $this->clientRepository->find($clientId);
        
        $companyData = $client->toArray();
        if (!empty($companyData['logo'])) {
            $companyData['logo'] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $companyData['logo'], '/');
        }

        return $companyData;
    }

    public function updateCompanyDetails(int $clientId, array $data, $logoFile = null)
    {
        if (isset($data['remove_logo']) && $data['remove_logo'] === 'true') {
            $data['logo'] = null;
        } elseif ($logoFile) {
            $path = $logoFile->store('company_logos', 'public');
            $data['logo'] = $path;
        }

        unset($data['remove_logo']);

        $this->clientRepository->update($clientId, $data);
        $client = $this->clientRepository->find($clientId);
        
        $companyData = $client->toArray();
        if (!empty($companyData['logo'])) {
            $companyData['logo'] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $companyData['logo'], '/');
        }
        
        return $companyData;
    }
}
