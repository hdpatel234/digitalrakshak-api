<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Api\Admin\StoreServiceFieldRequest;
use App\Http\Requests\Api\Admin\UpdateServiceFieldRequest;
use App\Services\ApiService\Admin\ServiceFieldService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceFieldController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ServiceFieldService $serviceFieldService
    ) {}

    /**
     * Display a listing of the service fields.
     */
    public function index(Request $request): JsonResponse
    {
        addInfoLog("Admin service field list request");

        $data = $this->serviceFieldService->getFields($request->all());

        return $this->success('Service fields retrieved successfully.', $data);
    }

    /**
     * Get statistics for service fields.
     */
    public function stats(Request $request): JsonResponse
    {
        addInfoLog("Admin service field stats request");

        $data = $this->serviceFieldService->getStats($request->all());

        return $this->success('Service fields stats retrieved successfully.', $data);
    }

    /**
     * Get distinct sections.
     */
    public function sections(Request $request): JsonResponse
    {
        addInfoLog("Admin service field sections request");

        $data = $this->serviceFieldService->getSections();

        return $this->success('Sections retrieved successfully.', $data);
    }

    /**
     * Store a newly created service field.
     */
    public function store(StoreServiceFieldRequest $request): JsonResponse
    {
        addInfoLog("Admin service field create request");

        try {
            $field = $this->serviceFieldService->storeField($request->validated());
            return $this->success('Service field created successfully.', $field, 201);
        } catch (\Exception $e) {
            $code = $e->getCode() === 422 ? 422 : 500;
            $errors = json_decode($e->getMessage(), true) ?? ['error' => [$e->getMessage()]];
            
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $errors
            ], $code);
        }
    }

    /**
     * Update the specified service field.
     */
    public function update(UpdateServiceFieldRequest $request, $id): JsonResponse
    {
        addInfoLog("Admin service field update request");

        try {
            $field = $this->serviceFieldService->updateField($id, $request->validated());
            return $this->success('Service field updated successfully.', $field);
        } catch (\Exception $e) {
            $code = $e->getCode() === 422 ? 422 : ($e->getCode() === 404 ? 404 : 500);
            
            if ($code === 422) {
                $errors = json_decode($e->getMessage(), true) ?? ['error' => [$e->getMessage()]];
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $errors
                ], $code);
            }
            
            return $this->error($e->getMessage() ?: 'Failed to update service field.', $code);
        }
    }

    /**
     * Remove the specified service field.
     */
    public function destroy($id): JsonResponse
    {
        addInfoLog("Admin service field delete request");

        try {
            $this->serviceFieldService->deleteField($id);
            return $this->success('Service field deleted successfully.');
        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->error($e->getMessage() ?: 'Failed to delete service field.', $code);
        }
    }
}
