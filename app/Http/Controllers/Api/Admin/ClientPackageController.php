<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ApiService\Admin\ClientPackageService;
use App\Traits\ApiResponse;

class ClientPackageController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ClientPackageService $clientPackageService
    ) {}

    /**
     * Display a listing of the client packages.
     */
    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin client package list request");

        $data = $this->clientPackageService->getClientPackages($request->all());

        return $this->success('Client packages retrieved successfully.', $data);
    }

    /**
     * Remove the specified package.
     */
    public function destroy(int $id): JsonResponse
    {
        addInfoLog("Admin client package destroy request");

        try {
            $this->clientPackageService->deleteClientPackage($id);

            return $this->success('Client package deleted successfully.');
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            // The trait error method expects: ($message, $code, $data)
            return $this->error($e->getMessage() ?: 'Failed to delete package.', $code, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Toggle status of the specified package.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        addInfoLog("Admin client package toggle status request");

        try {
            $package = $this->clientPackageService->toggleClientPackageStatus($id);

            return $this->success('Package status updated successfully.', [
                'status' => $package->status
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->error($e->getMessage() ?: 'Package not found.', $code, ['error' => $e->getMessage()]);
        }
    }
}
