<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Admin\StoreClientRequest;
use App\Http\Requests\Api\Admin\UpdateClientRequest;
use App\Http\Requests\Api\Admin\SetClientPricingRequest;
use App\Http\Requests\Api\Admin\ToggleClientStatusRequest;
use App\Services\ApiService\Admin\ClientService;
use App\Traits\ApiResponse;

class ClientController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ClientService $clientService
    ) {}

    /**
     * Display a listing of the clients.
     */
    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin client list request");

        $data = $this->clientService->getClients($request->all());

        return $this->success('Clients retrieved successfully.', $data);
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        addInfoLog("Admin client store request");

        try {
            $client = $this->clientService->storeClient(
                $request->validated(),
                $request->file('logo')
            );

            return $this->success('Client created successfully.', $client, 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create client.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): JsonResponse
    {
        addInfoLog("Admin client show request");

        $clientData = $this->clientService->getClient($client);

        return $this->success('Client retrieved successfully.', $clientData);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        addInfoLog("Admin client update request");

        try {
            $updatedClient = $this->clientService->updateClient(
                $client,
                $request->validated(),
                $request->file('logo')
            );

            return $this->success('Client updated successfully.', $updatedClient);
        } catch (\Exception $e) {
            return $this->error('Failed to update client.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Toggle the status of the specified client.
     */
    public function toggleStatus(ToggleClientStatusRequest $request, Client $client): JsonResponse
    {
        addInfoLog("Admin client toggle status request");

        $validated = $request->validated();

        $updatedClient = $this->clientService->toggleClientStatus($client, $validated['status']);

        return $this->success('Client status updated successfully.', $updatedClient);
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        addInfoLog("Admin client destroy request");

        $this->clientService->deleteClient($client);

        return $this->success('Client deleted successfully.');
    }

    /**
     * Get client pricing and services.
     */
    public function getClientPricing(Client $client): JsonResponse
    {
        addInfoLog("Admin client get pricing request");

        $data = $this->clientService->getClientPricing($client);

        return $this->success('Client pricing retrieved successfully.', $data);
    }

    /**
     * Set client pricing and services.
     */
    public function setPricing(SetClientPricingRequest $request, Client $client): JsonResponse
    {
        addInfoLog("Admin client set pricing request");

        $validated = $request->validated();

        try {
            $this->clientService->setPricing($client, $validated['services']);

            return $this->success('Client pricing updated successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to update client pricing.', 500, ['error' => $e->getMessage()]);
        }
    }
}
