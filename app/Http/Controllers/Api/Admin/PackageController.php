<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackageController extends BaseController
{
    /**
     * Display a listing of the packages.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Package::query()->where('type', 'admin');

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('package_name', 'LIKE', "%{$search}%")
                  ->orWhere('package_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('limit', 10);
        $packages = $query->paginate($perPage);
        
        $mappedPackages = collect($packages->items())->map(function ($package) {
            $data = $package->toArray();
            // Fetch available candidates if needed (can be implemented later or similar to Client PackageService)
            // For now just pass services as empty array if none, or we can load package services if they are mapped
            $data['services'] = [];
            $data['available_candidates'] = 0;
            return $data;
        });

        return response()->json([
            'status' => true,
            'message' => 'Packages retrieved successfully.',
            'data' => [
                'list' => $mappedPackages,
                'pagination' => [
                    'total' => $packages->total(),
                    'per_page' => $packages->perPage(),
                    'current_page' => $packages->currentPage(),
                    'last_page' => $packages->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created package.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'package_name' => 'required|string|max:255',
            'package_code' => 'required|string|max:255|unique:packages,package_code',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['type'] = 'admin';
        $data['client_id'] = 0;
        $data['final_price'] = $data['total_price'];
        $data['is_active'] = 1;
        $data['created_by'] = $request->user('api')?->id;

        $package = Package::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Package created successfully.',
            'data' => $package
        ], 201);
    }
}
