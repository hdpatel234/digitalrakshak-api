<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Admin\StoreClientRequest;
use App\Http\Requests\Api\Admin\UpdateClientRequest;

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
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('clients/logos', 'public');
        }
        
        $client = Client::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Client created successfully.',
            'data' => $client
        ], 201);
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Client retrieved successfully.',
            'data' => $client
        ]);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('clients/logos', 'public');
        }
        
        $client->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Client updated successfully.',
            'data' => $client
        ]);
    }

    /**
     * Toggle the status of the specified client.
     */
    public function toggleStatus(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $client->status = $validated['status'];
        $client->save();

        return response()->json([
            'status' => true,
            'message' => 'Client status updated successfully.',
            'data' => $client
        ]);
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json([
            'status' => true,
            'message' => 'Client deleted successfully.'
        ]);
    }
}
