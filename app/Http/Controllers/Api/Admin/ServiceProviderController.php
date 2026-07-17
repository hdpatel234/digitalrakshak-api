<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ServiceProvider;
use App\Http\Requests\Api\Admin\StoreServiceProviderRequest;
use App\Http\Requests\Api\Admin\UpdateServiceProviderRequest;
use App\Http\Requests\Api\Admin\ToggleServiceProviderStatusRequest;
use App\Services\ApiService\Admin\ServiceProviderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceProviderController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ServiceProviderService $serviceProviderService
    ) {}

    /**
     * Display a listing of the service providers.
     */
    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin service provider list request");

        $data = $this->serviceProviderService->getProviders($request->all());

        return $this->success('Service providers retrieved successfully.', $data);
    }

    /**
     * Store a newly created service provider in storage.
     */
    public function store(StoreServiceProviderRequest $request): JsonResponse
    {
        addInfoLog("Admin service provider create request");

        $provider = $this->serviceProviderService->storeProvider($request->validated());

        return $this->success('Service provider created successfully.', $provider, 201);
    }

    /**
     * Display the specified service provider.
     */
    public function show(ServiceProvider $serviceProvider): JsonResponse
    {
        addInfoLog("Admin service provider show request");

        $provider = $this->serviceProviderService->showProvider($serviceProvider);

        return $this->success('Service provider retrieved successfully.', $provider);
    }

    /**
     * Update the specified service provider in storage.
     */
    public function update(UpdateServiceProviderRequest $request, ServiceProvider $serviceProvider): JsonResponse
    {
        addInfoLog("Admin service provider update request");

        $provider = $this->serviceProviderService->updateProvider($serviceProvider, $request->validated());

        return $this->success('Service provider updated successfully.', $provider);
    }

    /**
     * Toggle the status of the specified service provider.
     */
    public function toggleStatus(ToggleServiceProviderStatusRequest $request, ServiceProvider $serviceProvider): JsonResponse
    {
        addInfoLog("Admin service provider toggle status request");

        $provider = $this->serviceProviderService->toggleProviderStatus($serviceProvider, $request->validated()['status']);

        return $this->success('Service provider status updated successfully.', $provider);
    }

    /**
     * Remove the specified service provider from storage.
     */
    public function destroy(ServiceProvider $serviceProvider): JsonResponse
    {
        addInfoLog("Admin service provider delete request");

        $this->serviceProviderService->deleteProvider($serviceProvider);

        return $this->success('Service provider deleted successfully.');
    }
}
