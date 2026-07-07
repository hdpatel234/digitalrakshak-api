<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientPackageController extends BaseController
{
    /**
     * Display a listing of the client packages.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Package::with('client')->where('type', 'client');

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('package_name', 'LIKE', "%{$search}%")
                  ->orWhere('package_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Handle sorting by client name
        if ($sortBy === 'client_name') {
            $query->join('clients', 'packages.client_id', '=', 'clients.id')
                  ->select('packages.*')
                  ->orderBy('clients.name', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination
        $perPage = $request->get('limit', 10);
        $packages = $query->paginate($perPage);
        
        $packageIds = collect($packages->items())->pluck('id')->toArray();
        
        $packageServices = \App\Models\PackageService::whereIn('package_id', $packageIds)
            ->whereIn('status', ['active', 1])
            ->get();
            
        $serviceIds = $packageServices->pluck('service_id')->unique()->toArray();
        $services = \App\Models\Service::whereIn('id', $serviceIds)->get()->keyBy('id');
        
        $servicesByPackageId = [];
        foreach ($packageServices as $ps) {
            $service = $services->get($ps->service_id);
            if ($service) {
                if (!isset($servicesByPackageId[$ps->package_id])) {
                    $servicesByPackageId[$ps->package_id] = [];
                }
                $servicesByPackageId[$ps->package_id][] = [
                    'service_id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_code' => $service->service_code,
                    'base_price' => $service->base_price,
                ];
            }
        }
        
        $mappedPackages = collect($packages->items())->map(function ($package) use ($servicesByPackageId) {
            $data = $package->toArray();
            $data['services'] = $servicesByPackageId[$package->id] ?? [];
            $data['available_candidates'] = 0; // This can be updated if we need actual logic
            return $data;
        });

        return response()->json([
            'status' => true,
            'message' => 'Client packages retrieved successfully.',
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
     * Remove the specified package.
     */
    public function destroy(int $id): JsonResponse
    {
        $package = Package::where('type', 'client')->find($id);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => 'Package not found.'
            ], 404);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            \App\Models\PackageService::where('package_id', $package->id)->delete();
            $package->delete();
            
            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Client package deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete package.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle status of the specified package.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $package = Package::where('type', 'client')->find($id);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => 'Package not found.'
            ], 404);
        }

        $package->status = $package->status === 'active' ? 'inactive' : 'active';
        $package->is_active = $package->status === 'active' ? 1 : 0;
        $package->save();

        return response()->json([
            'status' => true,
            'message' => 'Package status updated successfully.',
            'data' => [
                'status' => $package->status
            ]
        ]);
    }
}
