<?php

namespace App\Http\Controllers\Api\Client\Package;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Package\StorePackageRequest;
use App\Services\ApiService\Client\PackageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected PackageService $packageService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $result = $this->packageService->getPackages($request->all(), $clientId);

        return $this->success('Packages fetched successfully.', $result);
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->packageService->createPackage($request->validated(), $clientId, $user);

            return $this->success('Package created successfully.', $result, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(StorePackageRequest $request, int $package): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->packageService->updatePackage($package, $request->validated(), $clientId, $user);

            return $this->success('Package updated successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $request, int $package): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->packageService->getPackage($package, $clientId);

            return $this->success('Package fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function services(Request $request, int $package): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->packageService->getPackageServices($package, $clientId);

            return $this->success('Package services fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function candidates(Request $request, int $package): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $result = $this->packageService->getPackageCandidates($package, $clientId);

            return $this->success('Package candidates fetched successfully.', $result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
