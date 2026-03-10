<?php

namespace App\Http\Controllers\Api\Client\Service;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\ClientServicePricing;
use App\Services\ServiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServicesController extends BaseController
{
    use ApiResponse;
    protected ServiceService $service;

    public function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $serviceTable = $this->service->query()->getModel()->getTable();
        $statusColumn = $this->service->status();
        $basePriceColumn = $this->service->basePrice();

        $customPriceSubQuery = ClientServicePricing::query()
            ->select(ClientServicePricing::CUSTOM_PRICE)
            ->whereColumn(ClientServicePricing::SERVICE_ID, $serviceTable . '.id')
            ->where(ClientServicePricing::CLIENT_ID, $clientId)
            ->where(function ($query) {
                $query->where(ClientServicePricing::STATUS, 'active')
                    ->orWhere(ClientServicePricing::STATUS, 1);
            })
            ->where(function ($query) {
                $query->whereNull(ClientServicePricing::EFFECTIVE_FROM)
                    ->orWhereDate(ClientServicePricing::EFFECTIVE_FROM, '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull(ClientServicePricing::EFFECTIVE_TO)
                    ->orWhereDate(ClientServicePricing::EFFECTIVE_TO, '>=', now()->toDateString());
            })
            ->orderByDesc(ClientServicePricing::EFFECTIVE_FROM)
            ->orderByDesc('id')
            ->limit(1);

        $query = $this->service->query()
            ->where(function ($builder) use ($statusColumn) {
                $builder->where($statusColumn, 'active')
                    ->orWhere($statusColumn, 1);
            })
            ->select($serviceTable . '.*')
            ->selectSub($customPriceSubQuery, 'custom_price');

        $result = $this->service->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
                    $this->service->serviceName(),
                    $this->service->serviceCode(),
                    $this->service->description(),
                ],
                'status_column' => $this->service->status(),
                'date_column' => $this->service->createdAt(),
                'allowed_sorts' => [
                    $this->service->id(),
                    $this->service->serviceName(),
                    $this->service->serviceCode(),
                    $this->service->basePrice(),
                    $this->service->createdAt(),
                ],
                'default_sort_by' => $this->service->serviceName(),
                'default_sort_direction' => 'asc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $result['list'] = collect($result['list'])
            ->map(function ($item) use ($basePriceColumn) {
                $service = is_array($item) ? $item : $item->toArray();
                $customPrice = $service['custom_price'] ?? null;
                $basePrice = $service[$basePriceColumn] ?? null;

                $service['price'] = $customPrice ?? $basePrice;
                $service['is_custom_price'] = $customPrice !== null;

                return $service;
            })
            ->values()
            ->all();

        return $this->success('Services fetched successfully.', $result);
    }
}
