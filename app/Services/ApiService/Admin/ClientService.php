<?php

namespace App\Services\ApiService\Admin;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function getClients(array $data)
    {
        $query = Client::query();

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $clients = $query->paginate($perPage);

        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $inactiveClients = Client::where('status', 'inactive')->count();
        $suspendedClients = Client::where('status', 'suspended')->count();

        return [
            'list' => $clients->items(),
            'pagination' => [
                'total' => $clients->total(),
                'per_page' => $clients->perPage(),
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
            ],
            'statistics' => [
                'total' => $totalClients,
                'active' => $activeClients,
                'inactive' => $inactiveClients,
                'suspended' => $suspendedClients,
            ],
            'status_list' => [
                ['key' => 'active', 'name' => 'Active'],
                ['key' => 'inactive', 'name' => 'Inactive'],
                ['key' => 'suspended', 'name' => 'Suspended'],
            ]
        ];
    }

    public function storeClient(array $data, ?object $logoFile = null)
    {
        if ($logoFile) {
            $data['logo'] = $logoFile->store('clients/logos', 'public');
        }
        
        DB::beginTransaction();
        try {
            $client = Client::create($data);

            if (isset($data['services'])) {
                $servicesJson = $data['services'];
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

            DB::commit();
            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getClient(Client $client)
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
        return $clientData;
    }

    public function updateClient(Client $client, array $data, ?object $logoFile = null)
    {
        if ($logoFile) {
            $data['logo'] = $logoFile->store('clients/logos', 'public');
        }
        
        DB::beginTransaction();
        try {
            $client->update($data);

            if (isset($data['services'])) {
                $servicesJson = $data['services'];
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

            DB::commit();
            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function toggleClientStatus(Client $client, string $status)
    {
        $client->status = $status;
        $client->save();
        return $client;
    }

    public function deleteClient(Client $client)
    {
        $client->delete();
        return true;
    }

    public function getClientPricing(Client $client)
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

        return $data->values();
    }

    public function setPricing(Client $client, array $servicesData)
    {
        DB::beginTransaction();

        try {
            foreach ($servicesData as $serviceData) {
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

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
