<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::query();

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('service_name', 'LIKE', "%{$search}%")
                  ->orWhere('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Category filtering (optional but good to have)
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('service_category', $request->category);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('limit', 10);
        $services = $query->with('category')->paginate($perPage);

        $mappedServices = collect($services->items())->map(function ($service) {
            $data = $service->toArray();
            $data['service_category_name'] = $service->category ? $service->category->category_name : null;
            return $data;
        });

        return response()->json([
            'status' => true,
            'message' => 'Services retrieved successfully.',
            'data' => [
                'list' => $mappedServices,
                'pagination' => [
                    'total' => $services->total(),
                    'per_page' => $services->perPage(),
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                ],
                'stats' => [
                    'total' => Service::count(),
                    'active' => Service::where('status', 'active')->count(),
                    'inactive' => Service::where('status', 'inactive')->count(),
                    'categories' => Service::whereNotNull('service_category')->distinct('service_category')->count('service_category'),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'service_name' => 'required|string|max:255',
            'service_code' => 'required|string|max:255|unique:services,service_code',
            'service_category' => 'required|exists:service_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = Service::create($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Service created successfully.',
            'data' => $service
        ], 201);
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'service_name' => 'sometimes|required|string|max:255',
            'service_code' => 'sometimes|required|string|max:255|unique:services,service_code,' . $service->id,
            'service_category' => 'sometimes|required|exists:service_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'base_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $service->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Service updated successfully.',
            'data' => $service
        ], 200);
    }
}
