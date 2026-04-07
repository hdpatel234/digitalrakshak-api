<?php

namespace App\Services\ApiService\Client;

use App\Repositories\ClientRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;

class CompanyService extends BaseService
{
    protected ClientRepository $clientRepository;
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    public function index()
    {
        $clientId = Auth::user()->client_id;
        $client = $this->clientRepository->find($clientId);
        return $client;
    }
    public function update($id, array $data)
    {
        $client = $this->clientRepository->find($id);
        $client->update($data);
        return $client;
    }
}
