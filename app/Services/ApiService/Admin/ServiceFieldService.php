<?php

namespace App\Services\ApiService\Admin;

use App\Models\ServiceField;

class ServiceFieldService
{
    public function getFields(array $data)
    {
        $query = ServiceField::query();

        // Filter by service
        if (isset($data['service_id']) && !empty($data['service_id']) && $data['service_id'] !== 'all') {
            $query->where('service_id', $data['service_id']);
        }

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('field_name', 'LIKE', "%{$search}%")
                    ->orWhere('field_label', 'LIKE', "%{$search}%");
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
        $query = ServiceField::query();

        if (isset($data['service_id']) && !empty($data['service_id']) && $data['service_id'] !== 'all') {
            $query->where('service_id', $data['service_id']);
        }

        return [
            'total_fields' => (clone $query)->count(),
            'active_fields' => (clone $query)->where('status', '!=', 'inactive')->count(),
            'required_fields' => (clone $query)->where('is_required', true)->count(),
            'text_inputs' => (clone $query)->whereIn('field_type', ['text', 'textarea', 'Text', 'Textarea'])->count(),
            'number_inputs' => (clone $query)->whereIn('field_type', ['number', 'Number'])->count(),
            'file_uploads' => (clone $query)->whereIn('field_type', ['file', 'File'])->count(),
            'selections' => (clone $query)->whereIn('field_type', ['dropdown', 'Dropdown', 'select', 'Select'])->count(),
            'email_fields' => (clone $query)->whereIn('field_type', ['email', 'Email'])->count(),
        ];
    }

    public function getSections()
    {
        $defaultSections = ['Employment', 'Identity', 'Address', 'Education'];
        $dbSections = ServiceField::whereNotNull('section')
            ->where('section', '!=', '')
            ->distinct()
            ->pluck('section')
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
        $existing = ServiceField::where('service_id', $data['service_id'])->where('field_name', $data['field_name'])->first();
        if ($existing) {
            throw new \Exception(json_encode(['field_name' => ['Field name already exists for this service.']]), 422);
        }

        return ServiceField::create($data);
    }

    public function updateField(int $id, array $data)
    {
        $field = ServiceField::find($id);

        if (!$field) {
            throw new \Exception('Service field not found.', 404);
        }

        // Ensure field_name is unique per service (excluding current)
        $existing = ServiceField::where('service_id', $data['service_id'])
            ->where('field_name', $data['field_name'])
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            throw new \Exception(json_encode(['field_name' => ['Field name already exists for this service.']]), 422);
        }

        $field->update($data);
        return $field;
    }

    public function deleteField(int $id)
    {
        $field = ServiceField::find($id);

        if (!$field) {
            throw new \Exception('Service field not found.', 404);
        }

        $field->delete();
        return true;
    }
}
