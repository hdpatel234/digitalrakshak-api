<?php

namespace App\Http\Controllers\Api\Client\Service;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\ClientService;
use App\Models\ClientServicePricing;
use App\Models\ServiceCategory;
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
        $serviceCategoryColumn = $this->service->serviceCategory();
        $clientServicesTable = (new ClientService())->getTable();
        $serviceCategoriesTable = (new ServiceCategory())->getTable();
        $qualifiedStatusColumn = $serviceTable . '.' . $statusColumn;

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
            ->where(function ($builder) use ($qualifiedStatusColumn) {
                $builder->where($qualifiedStatusColumn, 'active')
                    ->orWhere($qualifiedStatusColumn, 1);
            })
            ->whereExists(function ($builder) use ($clientServicesTable, $serviceTable, $clientId) {
                $builder->selectRaw('1')
                    ->from($clientServicesTable)
                    ->whereColumn($clientServicesTable . '.' . ClientService::SERVICE_ID, $serviceTable . '.id')
                    ->where($clientServicesTable . '.' . ClientService::CLIENT_ID, $clientId)
                    ->where(function ($statusBuilder) use ($clientServicesTable) {
                        $statusBuilder->where($clientServicesTable . '.' . ClientService::STATUS, 'active')
                            ->orWhere($clientServicesTable . '.' . ClientService::STATUS, 1);
                    });
            })
            ->leftJoin(
                $serviceCategoriesTable,
                $serviceCategoriesTable . '.id',
                '=',
                $serviceTable . '.' . $serviceCategoryColumn
            )
            ->select($serviceTable . '.*')
            ->addSelect($serviceCategoriesTable . '.' . ServiceCategory::CATEGORY_NAME . ' as service_category_name')
            ->selectSub($customPriceSubQuery, 'custom_price');

        $result = $this->service->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
                    $serviceCategoriesTable . '.' . ServiceCategory::CATEGORY_NAME,
                    $serviceTable . '.' . $this->service->serviceName(),
                    $serviceTable . '.' . $this->service->serviceCode(),
                    $serviceTable . '.' . $this->service->description(),
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $serviceTable . '.' . $this->service->createdAt(),
                'allowed_sorts' => [
                    $serviceTable . '.' . $this->service->id(),
                    $serviceCategoriesTable . '.' . ServiceCategory::CATEGORY_NAME,
                    $serviceTable . '.' . $this->service->serviceName(),
                    $serviceTable . '.' . $this->service->serviceCode(),
                    $serviceTable . '.' . $this->service->basePrice(),
                    $serviceTable . '.' . $this->service->createdAt(),
                ],
                'default_sort_by' => $serviceTable . '.' . $this->service->serviceName(),
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
