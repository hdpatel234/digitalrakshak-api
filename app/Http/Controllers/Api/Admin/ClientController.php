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
                ],
                'status_list' => [
                    ['key' => 'active', 'name' => 'Active'],
                    ['key' => 'inactive', 'name' => 'Inactive'],
                    ['key' => 'suspended', 'name' => 'Suspended'],
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
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $client = Client::create($data);

            if ($request->has('services')) {
                $servicesJson = $request->input('services');
                $services = is_string($servicesJson) ? json_decode($servicesJson, true) : $servicesJson;

                if (is_array($services)) {
                    foreach ($services as $serviceData) {
                        $serviceId = $serviceData['service_id'] ?? null;
                        if (!$serviceId) continue;

                        $isEnabled = $serviceData['is_enabled'] ?? true;
                        $customPrice = $serviceData['custom_price'] ?? null;

                        \App\Models\ClientService::updateOrCreate(
                            ['client_id' => $client->id, 'service_id' => $serviceId],
                            ['status' => $isEnabled ? 'active' : 'inactive']
                        );

                        if ($customPrice !== null && $customPrice !== '') {
                            \App\Models\ClientServicePricing::updateOrCreate(
                                ['client_id' => $client->id, 'service_id' => $serviceId],
                                [
                                    'custom_price' => $customPrice,
                                    'effective_from' => now()->toDateString()
                                ]
                            );
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Client created successfully.',
                'data' => $client
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create client.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): JsonResponse
    {
        $clientServices = \App\Models\ClientService::where('client_id', $client->id)->get()->keyBy('service_id');
        $clientPricing = \App\Models\ClientServicePricing::where('client_id', $client->id)->get()->keyBy('service_id');

        $services = [];
        foreach ($clientServices as $serviceId => $clientService) {
            if ($clientService->status === 'active') {
                $customPrice = null;
                if ($clientPricing->has($serviceId)) {
                    $customPrice = $clientPricing->get($serviceId)->custom_price;
                }
                $services[] = [
                    'service_id' => (string) $serviceId,
                    'is_enabled' => true,
                    'custom_price' => $customPrice !== null ? (float) $customPrice : ''
                ];
            }
        }

        $clientData = $client->toArray();
        $clientData['services'] = $services;

        return response()->json([
            'status' => true,
            'message' => 'Client retrieved successfully.',
            'data' => $clientData
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
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $client->update($data);

            if ($request->has('services')) {
                $servicesJson = $request->input('services');
                $services = is_string($servicesJson) ? json_decode($servicesJson, true) : $servicesJson;

                if (is_array($services)) {
                    $providedServiceIds = [];

                    foreach ($services as $serviceData) {
                        $serviceId = $serviceData['service_id'] ?? null;
                        if (!$serviceId) continue;
                        
                        $providedServiceIds[] = $serviceId;

                        $isEnabled = $serviceData['is_enabled'] ?? true;
                        $customPrice = $serviceData['custom_price'] ?? null;

                        \App\Models\ClientService::updateOrCreate(
                            ['client_id' => $client->id, 'service_id' => $serviceId],
                            ['status' => $isEnabled ? 'active' : 'inactive']
                        );

                        if ($customPrice !== null && $customPrice !== '') {
                            \App\Models\ClientServicePricing::updateOrCreate(
                                ['client_id' => $client->id, 'service_id' => $serviceId],
                                [
                                    'custom_price' => $customPrice,
                                    'effective_from' => now()->toDateString()
                                ]
                            );
                        } else {
                            \App\Models\ClientServicePricing::where('client_id', $client->id)
                                ->where('service_id', $serviceId)
                                ->delete();
                        }
                    }
                    
                    // Set status to inactive for services not in the list
                    if (!empty($providedServiceIds)) {
                        \App\Models\ClientService::where('client_id', $client->id)
                            ->whereNotIn('service_id', $providedServiceIds)
                            ->update(['status' => 'inactive']);
                    } else {
                        \App\Models\ClientService::where('client_id', $client->id)
                            ->update(['status' => 'inactive']);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Client updated successfully.',
                'data' => $client
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update client.',
                'error' => $e->getMessage()
            ], 500);
        }
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

    /**
     * Get client pricing and services.
     */
    public function getClientPricing(Client $client): JsonResponse
    {
        $services = \App\Models\Service::all();
        $clientServices = \App\Models\ClientService::where('client_id', $client->id)->get()->keyBy('service_id');
        $clientPricing = \App\Models\ClientServicePricing::where('client_id', $client->id)->get()->keyBy('service_id');

        $data = $services->map(function ($service) use ($clientServices, $clientPricing) {
            $is_enabled = false;
            if ($clientServices->has($service->id)) {
                $is_enabled = $clientServices->get($service->id)->status === 'active';
            }

            $custom_price = null;
            if ($clientPricing->has($service->id)) {
                $custom_price = $clientPricing->get($service->id)->custom_price;
            }

            return [
                'id' => (string) $service->id,
                'service_name' => $service->service_name,
                'base_price' => (float) $service->base_price,
                'custom_price' => $custom_price !== null ? (float) $custom_price : '',
                'is_enabled' => $is_enabled,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Client pricing retrieved successfully.',
            'data' => $data->values()
        ]);
    }

    /**
     * Set client pricing and services.
     */
    public function setPricing(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.is_enabled' => 'required|boolean',
            'services.*.custom_price' => 'nullable|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            foreach ($validated['services'] as $serviceData) {
                $serviceId = $serviceData['service_id'];
                $isEnabled = $serviceData['is_enabled'];
                $customPrice = $serviceData['custom_price'];

                // Update or Create ClientService
                \App\Models\ClientService::updateOrCreate(
                    ['client_id' => $client->id, 'service_id' => $serviceId],
                    ['status' => $isEnabled ? 'active' : 'inactive']
                );

                // Update or Create ClientServicePricing
                if ($customPrice !== null && $customPrice !== '') {
                    \App\Models\ClientServicePricing::updateOrCreate(
                        ['client_id' => $client->id, 'service_id' => $serviceId],
                        [
                            'custom_price' => $customPrice,
                            'effective_from' => now()->toDateString()
                        ]
                    );
                } else {
                    \App\Models\ClientServicePricing::where('client_id', $client->id)
                        ->where('service_id', $serviceId)
                        ->delete();
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Client pricing updated successfully.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update client pricing.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
