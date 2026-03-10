<?php
namespace App\Http\Controllers\Api\Client\Package;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\PackageService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PackageController extends BaseController
{
    use ApiResponse;
    protected PackageService $service;

    public function __construct(PackageService $service)
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

        $packageTable = $this->service->query()->getModel()->getTable();
        $statusColumn = $this->service->status();
        $isActiveColumn = $this->service->isActive();
        $clientIdColumn = $this->service->clientId();
        $packageNameColumn = $this->service->packageName();
        $packageCodeColumn = $this->service->packageCode();
        $descriptionColumn = $this->service->description();
        $typeColumn = $this->service->type();
        $totalPriceColumn = $this->service->totalPrice();
        $finalPriceColumn = $this->service->finalPrice();

        $qualifiedStatusColumn = $packageTable . '.' . $statusColumn;
        $qualifiedIsActiveColumn = $packageTable . '.' . $isActiveColumn;
        $qualifiedClientIdColumn = $packageTable . '.' . $clientIdColumn;

        $query = $this->service->query()
            ->where(function ($builder) use ($qualifiedStatusColumn) {
                $builder->where($qualifiedStatusColumn, 'active')
                    ->orWhere($qualifiedStatusColumn, 1);
            })
            ->where(function ($builder) use ($qualifiedIsActiveColumn) {
                $builder->where($qualifiedIsActiveColumn, 'active')
                    ->orWhere($qualifiedIsActiveColumn, 1);
            })
            ->where(function ($builder) use ($qualifiedClientIdColumn, $clientId) {
                $builder->where($qualifiedClientIdColumn, $clientId)
                    ->orWhere($qualifiedClientIdColumn, 0);
            });

        $result = $this->service->datatable(
            query: $query,
            params: $request->all(),
            config: [
                'searchable' => [
                    $packageTable . '.' . $packageNameColumn,
                    $packageTable . '.' . $packageCodeColumn,
                    $packageTable . '.' . $descriptionColumn,
                    $packageTable . '.' . $typeColumn,
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $packageTable . '.' . $this->service->createdAt(),
                'allowed_filters' => [
                    'type' => $packageTable . '.' . $typeColumn,
                    'client_id' => $qualifiedClientIdColumn,
                ],
                'allowed_sorts' => [
                    $packageTable . '.' . $this->service->id(),
                    $packageTable . '.' . $packageNameColumn,
                    $packageTable . '.' . $packageCodeColumn,
                    $packageTable . '.' . $typeColumn,
                    $packageTable . '.' . $totalPriceColumn,
                    $packageTable . '.' . $finalPriceColumn,
                    $packageTable . '.' . $this->service->createdAt(),
                ],
                'default_sort_by' => $packageTable . '.' . $packageNameColumn,
                'default_sort_direction' => 'asc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        $result['list'] = collect($result['list'])
            ->map(function ($item) use ($finalPriceColumn, $totalPriceColumn) {
                $package = is_array($item) ? $item : $item->toArray();
                $finalPrice = $package[$finalPriceColumn] ?? null;
                $totalPrice = $package[$totalPriceColumn] ?? null;

                $package['price'] = $finalPrice ?? $totalPrice;
                $package['is_discounted'] = $finalPrice !== null && $totalPrice !== null && (float) $finalPrice < (float) $totalPrice;

                return $package;
            })
            ->values()
            ->all();

        return $this->success('Packages fetched successfully.', $result);
    }
}
