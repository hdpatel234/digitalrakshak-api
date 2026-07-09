<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceProviderController extends BaseController
{
    /**
     * Display a listing of the service providers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceProvider::query();

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('provider_name', 'LIKE', "%{$search}%")
                  ->orWhere('provider_code', 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('limit', 10);
        $providers = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Service providers retrieved successfully.',
            'data' => [
                'list' => $providers->items(),
                'pagination' => [
                    'total' => $providers->total(),
                    'per_page' => $providers->perPage(),
                    'current_page' => $providers->currentPage(),
                    'last_page' => $providers->lastPage(),
                ],
                'status_list' => [
                    ['key' => 'active', 'name' => 'Active'],
                    ['key' => 'inactive', 'name' => 'Inactive'],
                    ['key' => 'maintenance', 'name' => 'Maintenance'],
                    ['key' => 'deprecated', 'name' => 'Deprecated'],
                ]
            ]
        ]);
    }

    /**
     * Store a newly created service provider in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider_name' => 'required|string|max:255',
            'provider_code' => 'required|string|max:100|unique:service_providers',
            'provider_type' => 'nullable|in:api,webhook,manual',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:500',
            'documentation_url' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive,maintenance,deprecated',
            'is_default' => 'nullable|boolean',
            'priority' => 'nullable|integer',
        ]);

        $provider = ServiceProvider::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Service provider created successfully.',
            'data' => $provider
        ], 201);
    }

    /**
     * Display the specified service provider.
     */
    public function show(ServiceProvider $serviceProvider): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Service provider retrieved successfully.',
            'data' => $serviceProvider
        ]);
    }

    /**
     * Update the specified service provider in storage.
     */
    public function update(Request $request, ServiceProvider $serviceProvider): JsonResponse
    {
        $validated = $request->validate([
            'provider_name' => 'required|string|max:255',
            'provider_code' => 'required|string|max:100|unique:service_providers,provider_code,' . $serviceProvider->id,
            'provider_type' => 'nullable|in:api,webhook,manual',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:500',
            'documentation_url' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive,maintenance,deprecated',
            'is_default' => 'nullable|boolean',
            'priority' => 'nullable|integer',
        ]);

        $serviceProvider->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Service provider updated successfully.',
            'data' => $serviceProvider
        ]);
    }

    /**
     * Toggle the status of the specified service provider.
     */
    public function toggleStatus(Request $request, ServiceProvider $serviceProvider): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,maintenance,deprecated'
        ]);

        $serviceProvider->status = $validated['status'];
        $serviceProvider->save();

        return response()->json([
            'status' => true,
            'message' => 'Service provider status updated successfully.',
            'data' => $serviceProvider
        ]);
    }

    /**
     * Remove the specified service provider from storage.
     */
    public function destroy(ServiceProvider $serviceProvider): JsonResponse
    {
        $serviceProvider->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service provider deleted successfully.'
        ]);
    }
}
