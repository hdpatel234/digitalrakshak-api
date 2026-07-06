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
                ]
            ]
        ]);
    }
}
