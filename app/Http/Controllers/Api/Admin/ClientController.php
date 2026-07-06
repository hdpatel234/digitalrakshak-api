<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Admin\StoreClientRequest;

class ClientController extends BaseController
{
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Client::query();

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
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
        $clients = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Clients retrieved successfully.',
            'data' => [
                'list' => $clients->items(),
                'pagination' => [
                    'total' => $clients->total(),
                    'per_page' => $clients->perPage(),
                    'current_page' => $clients->currentPage(),
                    'last_page' => $clients->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $client = Client::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Client created successfully.',
            'data' => $client
        ], 201);
    }
}
