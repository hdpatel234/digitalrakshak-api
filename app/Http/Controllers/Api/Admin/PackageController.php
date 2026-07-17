<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\StorePackageRequest;
use App\Http\Requests\Api\Admin\UpdatePackageRequest;
use App\Services\ApiService\Admin\PackageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackageController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected PackageService $packageService
    ) {}

    /**
     * Display a listing of the packages.
     */
    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin package list request");

        $data = $this->packageService->getPackages($request->all());

        return $this->success('Packages retrieved successfully.', $data);
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        addInfoLog("Admin package create request");

        try {
            $package = $this->packageService->storePackage($request->validated(), $request->user('api')?->id);

            return $this->success('Package created successfully.', $package, 201);
        } catch (\Exception $e) {
            $code = $e->getCode() === 422 ? 422 : 500;
            return $this->error($e->getMessage() ?: 'Failed to create package.', $code, ['error' => $e->getMessage()]);
        }
    }

    public function show(int $id): JsonResponse
    {
        addInfoLog("Admin package show request");

        try {
            $packageData = $this->packageService->showPackage($id);

            return $this->success('Package retrieved successfully.', $packageData);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage() ?: 'Package not found.', $code, ['error' => $e->getMessage()]);
        }
    }

    public function update(UpdatePackageRequest $request, int $id): JsonResponse
    {
        addInfoLog("Admin package update request");

        try {
            $package = $this->packageService->updatePackage($id, $request->validated(), $request->user('api')?->id);

            return $this->success('Package updated successfully.', $package);
        } catch (\Exception $e) {
            $code = $e->getCode() === 422 ? 422 : 500;
            return $this->error($e->getMessage() ?: 'Failed to update package.', $code, ['error' => $e->getMessage()]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        addInfoLog("Admin package delete request");

        try {
            $this->packageService->deletePackage($id);

            return $this->success('Package deleted successfully.');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage() ?: 'Failed to delete package.', $code, ['error' => $e->getMessage()]);
        }
    }

    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        addInfoLog("Admin package toggle status request");

        try {
            $package = $this->packageService->togglePackageStatus($id, $request->user('api')?->id);

            return $this->success("Package status updated to {$package->status}.", $package);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage() ?: 'Failed to update package status.', $code, ['error' => $e->getMessage()]);
        }
    }
}
