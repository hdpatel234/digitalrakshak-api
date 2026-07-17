<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ClientServicePricingRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function __construct(
        protected ClientRepository $repo,
        protected ClientServiceRepository $clientServiceRepo,
        protected ClientServicePricingRepository $clientServicePricingRepo,
        protected ServiceRepository $serviceRepo,
    ) {}
    public function getClients(array $data)
    {
        $query = $this->repo->getClientsQuery($data);

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $clients = $query->paginate($perPage);

        $totalClients = $this->repo->count();
        $activeClients = $this->repo->countByStatus('active');
        $inactiveClients = $this->repo->countByStatus('inactive');
        $suspendedClients = $this->repo->countByStatus('suspended');

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
            $client = $this->repo->create($data);

            if (isset($data['services'])) {
                $servicesJson = $data['services'];
                $services = is_string($servicesJson) ? json_decode($servicesJson, true) : $servicesJson;

                if (is_array($services)) {
                    foreach ($services as $serviceData) {
                        $serviceId = $serviceData['service_id'] ?? null;
                        if (!$serviceId) continue;

                        $isEnabled = $serviceData['is_enabled'] ?? true;
                        $customPrice = $serviceData['custom_price'] ?? null;

                        $this->clientServiceRepo->updateOrCreateByClientAndService(
                            $client->{$this->repo->id()},
                            $serviceId,
                            [$this->clientServiceRepo->status() => $isEnabled ? 'active' : 'inactive']
                        );

                        if ($customPrice !== null && $customPrice !== '') {
                            $this->clientServicePricingRepo->updateOrCreateByClientAndService(
                                $client->{$this->repo->id()},
                                $serviceId,
                                [
                                    $this->clientServicePricingRepo->customPrice() => $customPrice,
                                    $this->clientServicePricingRepo->effectiveFrom() => now()->toDateString()
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
        $clientServices = $this->clientServiceRepo->getByClientId($client->{$this->repo->id()});
        $clientPricing = $this->clientServicePricingRepo->getByClientId($client->{$this->repo->id()});

        $services = [];
        foreach ($clientServices as $serviceId => $clientService) {
            if ($clientService->{$this->clientServiceRepo->status()} === 'active') {
                $customPrice = null;
                if ($clientPricing->has($serviceId)) {
                    $customPrice = $clientPricing->get($serviceId)->{$this->clientServicePricingRepo->customPrice()};
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

                        $this->clientServiceRepo->updateOrCreateByClientAndService(
                            $client->{$this->repo->id()},
                            $serviceId,
                            [$this->clientServiceRepo->status() => $isEnabled ? 'active' : 'inactive']
                        );

                        if ($customPrice !== null && $customPrice !== '') {
                            $this->clientServicePricingRepo->updateOrCreateByClientAndService(
                                $client->{$this->repo->id()},
                                $serviceId,
                                [
                                    $this->clientServicePricingRepo->customPrice() => $customPrice,
                                    $this->clientServicePricingRepo->effectiveFrom() => now()->toDateString()
                                ]
                            );
                        } else {
                            $this->clientServicePricingRepo->deleteByClientAndService(
                                $client->{$this->repo->id()},
                                $serviceId
                            );
                        }
                    }
                    
                    // Set status to inactive for services not in the list
                    $this->clientServiceRepo->updateStatusNotInList(
                        $client->{$this->repo->id()},
                        $providedServiceIds,
                        'inactive'
                    );
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
        $client->{$this->repo->status()} = $status;
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
        $services = $this->serviceRepo->all();
        $clientServices = $this->clientServiceRepo->getByClientId($client->{$this->repo->id()});
        $clientPricing = $this->clientServicePricingRepo->getByClientId($client->{$this->repo->id()});

        $data = $services->map(function ($service) use ($clientServices, $clientPricing) {
            $is_enabled = false;
            if ($clientServices->has($service->{$this->serviceRepo->id()})) {
                $is_enabled = $clientServices->get($service->{$this->serviceRepo->id()})->{$this->clientServiceRepo->status()} === 'active';
            }

            $custom_price = null;
            if ($clientPricing->has($service->{$this->serviceRepo->id()})) {
                $custom_price = $clientPricing->get($service->{$this->serviceRepo->id()})->{$this->clientServicePricingRepo->customPrice()};
            }

            return [
                'id' => (string) $service->{$this->serviceRepo->id()},
                'service_name' => $service->{$this->serviceRepo->serviceName()},
                'base_price' => (float) $service->{$this->serviceRepo->basePrice()},
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
                $this->clientServiceRepo->updateOrCreateByClientAndService(
                    $client->{$this->repo->id()},
                    $serviceId,
                    [$this->clientServiceRepo->status() => $isEnabled ? 'active' : 'inactive']
                );

                // Update or Create ClientServicePricing
                if ($customPrice !== null && $customPrice !== '') {
                    $this->clientServicePricingRepo->updateOrCreateByClientAndService(
                        $client->{$this->repo->id()},
                        $serviceId,
                        [
                            $this->clientServicePricingRepo->customPrice() => $customPrice,
                            $this->clientServicePricingRepo->effectiveFrom() => now()->toDateString()
                        ]
                    );
                } else {
                    $this->clientServicePricingRepo->deleteByClientAndService(
                        $client->{$this->repo->id()},
                        $serviceId
                    );
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
