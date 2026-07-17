<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Service;
use App\Http\Requests\Api\Admin\StoreServiceRequest;
use App\Http\Requests\Api\Admin\UpdateServiceRequest;
use App\Services\ApiService\Admin\ServiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ServiceService $serviceService
    ) {}

    /**
     * Display a listing of the services.
     */
    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin service list request");

        $data = $this->serviceService->getServices($request->all());

        return $this->success('Services retrieved successfully.', $data);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): JsonResponse
    {
        addInfoLog("Admin service show request");

        $data = $this->serviceService->showService($service);

        return $this->success('Service retrieved successfully.', $data);
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        addInfoLog("Admin service create request");

        $service = $this->serviceService->storeService($request->validated());

        return $this->success('Service created successfully.', $service, 201);
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        addInfoLog("Admin service update request");

        $updatedService = $this->serviceService->updateService($service, $request->validated());

        return $this->success('Service updated successfully.', $updatedService);
    }
}
