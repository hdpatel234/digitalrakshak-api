<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ServicesFieldRepository;

class ServiceFieldService
{
    public function __construct(
        protected ServicesFieldRepository $repo
    ) {}
    public function getFields(array $data)
    {
        $query = $this->repo->query();

        // Filter by service
        if (isset($data['service_id']) && !empty($data['service_id']) && $data['service_id'] !== 'all') {
            $query->where($this->repo->serviceId(), $data['service_id']);
        }

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where($this->repo->fieldName(), 'LIKE', "%{$search}%")
                    ->orWhere($this->repo->fieldLabel(), 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? 'display_order';
        $sortDirection = $data['sort_direction'] ?? 'asc';
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $data['limit'] ?? 10;
        $fields = $query->with('service')->paginate($perPage);

        $mappedFields = collect($fields->items())->map(function ($field) {
            $data = $field->toArray();
            $data['service_name'] = $field->service ? $field->service->service_name : null;
            return $data;
        });

        return [
            'list' => $mappedFields,
            'pagination' => [
                'total' => $fields->total(),
                'per_page' => $fields->perPage(),
                'current_page' => $fields->currentPage(),
                'last_page' => $fields->lastPage(),
            ]
        ];
    }

    public function getStats(array $data)
    {
        $query = $this->repo->query();

        if (isset($data['service_id']) && !empty($data['service_id']) && $data['service_id'] !== 'all') {
            $query->where($this->repo->serviceId(), $data['service_id']);
        }

        return [
            'total_fields' => (clone $query)->count(),
            'active_fields' => (clone $query)->where($this->repo->status(), '!=', 'inactive')->count(),
            'required_fields' => (clone $query)->where($this->repo->isRequired(), true)->count(),
            'text_inputs' => (clone $query)->whereIn($this->repo->fieldType(), ['text', 'textarea', 'Text', 'Textarea'])->count(),
            'number_inputs' => (clone $query)->whereIn($this->repo->fieldType(), ['number', 'Number'])->count(),
            'file_uploads' => (clone $query)->whereIn($this->repo->fieldType(), ['file', 'File'])->count(),
            'selections' => (clone $query)->whereIn($this->repo->fieldType(), ['dropdown', 'Dropdown', 'select', 'Select'])->count(),
            'email_fields' => (clone $query)->whereIn($this->repo->fieldType(), ['email', 'Email'])->count(),
        ];
    }

    public function getSections()
    {
        $defaultSections = ['Employment', 'Identity', 'Address', 'Education'];
        $dbSections = $this->repo->query()->whereNotNull($this->repo->section())
            ->where($this->repo->section(), '!=', '')
            ->distinct()
            ->pluck($this->repo->section())
            ->toArray();

        $allSections = array_values(array_unique(array_merge($defaultSections, $dbSections)));

        $formatted = array_map(function ($sec) {
            return ['value' => $sec, 'label' => $sec];
        }, $allSections);

        return $formatted;
    }

    public function storeField(array $data)
    {
        if (!isset($data['display_order'])) {
            $data['display_order'] = 0;
        }

        // Ensure field_name is unique per service
        $existing = $this->repo->query()->where($this->repo->serviceId(), $data['service_id'])->where($this->repo->fieldName(), $data['field_name'])->first();
        if ($existing) {
            throw new \Exception(json_encode(['field_name' => ['Field name already exists for this service.']]), 422);
        }

        return $this->repo->create($data);
    }

    public function updateField(int $id, array $data)
    {
        $field = $this->repo->find($id);

        if (!$field) {
            throw new \Exception('Service field not found.', 404);
        }

        // Ensure field_name is unique per service (excluding current)
        $existing = $this->repo->query()->where($this->repo->serviceId(), $data['service_id'])
            ->where($this->repo->fieldName(), $data['field_name'])
            ->where($this->repo->id(), '!=', $id)
            ->first();

        if ($existing) {
            throw new \Exception(json_encode(['field_name' => ['Field name already exists for this service.']]), 422);
        }

        $field->update($data);
        return $field;
    }

    public function deleteField(int $id)
    {
        $field = $this->repo->find($id);

        if (!$field) {
            throw new \Exception('Service field not found.', 404);
        }

        $field->delete();
        return true;
    }
}
