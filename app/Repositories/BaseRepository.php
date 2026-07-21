<?php

namespace App\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

abstract class BaseRepository
{
    public function __construct(protected Model $model) {}

    // column constants
    public function id()
    {
        return $this->model->getKeyName();
    }

    public function createdBy()
    {
        return BaseModel::CREATED_BY;
    }
    public function updatedBy()
    {
        return BaseModel::UPDATED_BY;
    }
    public function deletedBy()
    {
        return BaseModel::DELETED_BY;
    }

    public function createdAt()
    {
        return BaseModel::CREATED_AT;
    }
    public function updatedAt()
    {
        return BaseModel::UPDATED_AT;
    }
    public function deletedAt()
    {
        return BaseModel::DELETED_AT;
    }

    // functions
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    public function allActive($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    public function count()
    {
        return $this->model->count();
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function query()
    {
        return $this->model->query();
    }

    public function datatable(?Builder $query = null, array $params = [], array $config = []): array
    {
        $builder = $query ?? $this->query();

        $defaultPerPage = (int) ($config['default_per_page'] ?? 10);
        $maxPerPage = (int) ($config['max_per_page'] ?? 100);
        $perPage = (int) ($params['per_page'] ?? $defaultPerPage);
        $perPage = max(1, min($perPage, $maxPerPage));

        $page = max(1, (int) ($params['page'] ?? 1));

        $search = trim((string) ($params['search'] ?? ''));
        $searchable = (array) ($config['searchable'] ?? []);
        $this->applySearch($builder, $search, $searchable);

        $filterInput = (array) ($params['filters'] ?? []);
        $flatFilterInput = array_merge(
            $params,
            $filterInput
        );

        $appliedFilters = $this->applyFilters($builder, $flatFilterInput, $config);
        $appliedFilters['search'] = $search;

        [$sortBy, $sortDirection] = $this->applySorting($builder, $params, $config);

        $paginator = $builder->paginate($perPage, ['*'], 'page', $page);

        return [
            'list' => $paginator->items() ?? [],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'filters' => $appliedFilters,
            'sorting' => [
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
            ],
        ];
    }

    protected function applySearch(Builder $query, string $search, array $searchable): void
    {
        if ($search === '' || $searchable === []) {
            return;
        }

        $query->where(function (Builder $builder) use ($search, $searchable) {
            foreach ($searchable as $searchableColumn) {
                if ($searchableColumn instanceof Closure) {
                    $searchableColumn($builder, $search);
                    continue;
                }

                $builder->orWhere($searchableColumn, 'like', '%' . $search . '%');
            }
        });
    }

    protected function applyFilters(Builder $query, array $input, array $config): array
    {
        $applied = [
            'search' => null,
            'status' => $input['status'] ?? null,
            'date_from' => $input['date_from'] ?? null,
            'date_to' => $input['date_to'] ?? null,
        ];

        $allowedFilters = (array) ($config['allowed_filters'] ?? []);

        foreach ($allowedFilters as $filterKey => $filterDefinition) {
            $value = $input[$filterKey] ?? null;
            $applied[$filterKey] = $value;

            if ($value === null || $value === '') {
                continue;
            }

            if ($filterDefinition instanceof Closure) {
                $filterDefinition($query, $value, $input);
                continue;
            }

            if (is_array($filterDefinition)) {
                $column = $filterDefinition[0] ?? $filterKey;
                $operator = $filterDefinition[1] ?? '=';
                $query->where($column, $operator, $value);
                continue;
            }

            $query->where($filterDefinition, $value);
        }

        $statusColumn = $config['status_column'] ?? null;
        $status = $input['status'] ?? null;
        if ($statusColumn && $status !== null && $status !== '') {
            $query->where($statusColumn, $status);
        }

        $dateColumn = $config['date_column'] ?? null;
        if ($dateColumn) {
            $dateFrom = $input['date_from'] ?? null;
            $dateTo = $input['date_to'] ?? null;

            if ($dateFrom) {
                $query->whereDate($dateColumn, '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate($dateColumn, '<=', $dateTo);
            }
        }

        return $applied;
    }

    protected function applySorting(Builder $query, array $params, array $config): array
    {
        $defaultSortBy = (string) ($config['default_sort_by'] ?? $this->createdAt());
        if ($defaultSortBy === '') {
            $defaultSortBy = $this->id();
        }

        $sortBy = (string) ($params['sort_by'] ?? $defaultSortBy);
        $sortDirection = strtolower((string) ($params['sort_direction'] ?? ($config['default_sort_direction'] ?? 'desc')));
        $sortDirection = in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc';

        $allowedSorts = (array) ($config['allowed_sorts'] ?? []);
        if ($allowedSorts !== [] && !in_array($sortBy, $allowedSorts, true)) {
            $sortBy = $defaultSortBy;
        }

        $query->orderBy($sortBy, $sortDirection);

        return [$sortBy, $sortDirection];
    }
}
