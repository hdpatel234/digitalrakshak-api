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

    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $serviceIds = array_values(array_unique($data['service_ids']));

        // Calculate total price based on services
        $services = \App\Models\Service::whereIn('id', $serviceIds)->get();
        if ($services->count() !== count($serviceIds)) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => ['service_ids' => ['Some services are invalid.']]
            ], 422);
        }

        $totalPrice = $services->sum('base_price');
        
        $packageCode = 'AP-' . strtoupper(\Illuminate\Support\Str::random(5));
        while (\App\Models\Package::where('package_code', $packageCode)->exists()) {
            $packageCode = 'AP-' . strtoupper(\Illuminate\Support\Str::random(5));
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $package = Package::create([
                'package_name' => $data['package_name'],
                'package_code' => $packageCode,
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'type' => 'admin',
                'client_id' => 0,
                'is_active' => 1,
                'status' => $data['status'] ?? 'active',
                'created_by' => $request->user('api')?->id,
            ]);

            foreach ($serviceIds as $index => $serviceId) {
                \App\Models\PackageService::create([
                    'package_id' => $package->id,
                    'service_id' => $serviceId,
                    'is_mandatory' => 1,
                    'display_order' => $index + 1,
                    'status' => 'active',
                    'created_by' => $request->user('api')?->id,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Package created successfully.',
                'data' => $package
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create package.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $package = \App\Models\Package::where('type', 'admin')->find($id);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => 'Package not found.'
            ], 404);
        }

        $packageData = $package->toArray();
        $packageData['price'] = $package->final_price ?? $package->total_price;

        $services = \App\Models\PackageService::where('package_id', $id)
            ->whereIn('status', ['active', 1])
            ->orderBy('display_order', 'asc')
            ->get();
            
        $packageData['services'] = $services->map(function ($row) {
            return [
                'id' => $row->id,
                'package_id' => $row->package_id,
                'service_id' => $row->service_id,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Package retrieved successfully.',
            'data' => $packageData
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $package = \App\Models\Package::where('type', 'admin')->find($id);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => 'Package not found.'
            ], 404);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $serviceIds = array_values(array_unique($data['service_ids']));

        // Calculate total price based on services
        $services = \App\Models\Service::whereIn('id', $serviceIds)->get();
        if ($services->count() !== count($serviceIds)) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => ['service_ids' => ['Some services are invalid.']]
            ], 422);
        }

        $totalPrice = $services->sum('base_price');
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $package->update([
                'package_name' => $data['package_name'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? $package->icon,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'status' => $data['status'] ?? $package->status,
                'updated_by' => $request->user('api')?->id,
            ]);

            \App\Models\PackageService::where('package_id', $package->id)->delete();

            foreach ($serviceIds as $index => $serviceId) {
                \App\Models\PackageService::create([
                    'package_id' => $package->id,
                    'service_id' => $serviceId,
                    'is_mandatory' => 1,
                    'display_order' => $index + 1,
                    'status' => 'active',
                    'created_by' => $package->created_by,
                    'updated_by' => $request->user('api')?->id,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Package updated successfully.',
                'data' => $package
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update package.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
