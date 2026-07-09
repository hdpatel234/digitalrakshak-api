<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ServiceField;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ServiceFieldController extends BaseController
{
    /**
     * Display a listing of the service fields.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceField::query();

        // Filter by service
        if ($request->has('service_id') && !empty($request->service_id) && $request->service_id !== 'all') {
            $query->where('service_id', $request->service_id);
        }

        // Search filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('field_name', 'LIKE', "%{$search}%")
                    ->orWhere('field_label', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'display_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('limit', 10);
        $fields = $query->with('service')->paginate($perPage);

        $mappedFields = collect($fields->items())->map(function ($field) {
            $data = $field->toArray();
            $data['service_name'] = $field->service ? $field->service->service_name : null;
            return $data;
        });

        return response()->json([
            'status' => true,
            'message' => 'Service fields retrieved successfully.',
            'data' => [
                'list' => $mappedFields,
                'pagination' => [
                    'total' => $fields->total(),
                    'per_page' => $fields->perPage(),
                    'current_page' => $fields->currentPage(),
                    'last_page' => $fields->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get statistics for service fields.
     */
    public function stats(Request $request): JsonResponse
    {
        $query = ServiceField::query();

        if ($request->has('service_id') && !empty($request->service_id) && $request->service_id !== 'all') {
            $query->where('service_id', $request->service_id);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'total_fields' => (clone $query)->count(),
                'active_fields' => (clone $query)->where('status', '!=', 'inactive')->count(),
                'required_fields' => (clone $query)->where('is_required', true)->count(),
                'text_inputs' => (clone $query)->whereIn('field_type', ['text', 'textarea', 'Text', 'Textarea'])->count(),
                'number_inputs' => (clone $query)->whereIn('field_type', ['number', 'Number'])->count(),
                'file_uploads' => (clone $query)->whereIn('field_type', ['file', 'File'])->count(),
                'selections' => (clone $query)->whereIn('field_type', ['dropdown', 'Dropdown', 'select', 'Select'])->count(),
                'email_fields' => (clone $query)->whereIn('field_type', ['email', 'Email'])->count(),
            ]
        ]);
    }

    /**
     * Get distinct sections.
     */
    public function sections(Request $request): JsonResponse
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

        return response()->json([
            'status' => true,
            'message' => 'Sections retrieved successfully.',
            'data' => $formatted
        ]);
    }

    /**
     * Store a newly created service field.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'field_name' => 'required|string|max:100',
            'section' => 'nullable|string|max:100',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|string|max:50',
            'is_required' => 'boolean',
            'validation_regex' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        if (!isset($data['display_order'])) {
            $data['display_order'] = 0;
        }

        // Ensure field_name is unique per service
        $existing = ServiceField::where('service_id', $data['service_id'])->where('field_name', $data['field_name'])->first();
        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => ['field_name' => ['Field name already exists for this service.']]
            ], 422);
        }

        $field = ServiceField::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Service field created successfully.',
            'data' => $field
        ], 201);
    }

    /**
     * Update the specified service field.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $field = ServiceField::find($id);

        if (!$field) {
            return response()->json([
                'status' => false,
                'message' => 'Service field not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'field_name' => 'required|string|max:100',
            'section' => 'nullable|string|max:100',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|string|max:50',
            'is_required' => 'boolean',
            'validation_regex' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Ensure field_name is unique per service (excluding current)
        $existing = ServiceField::where('service_id', $data['service_id'])
            ->where('field_name', $data['field_name'])
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => ['field_name' => ['Field name already exists for this service.']]
            ], 422);
        }

        $field->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Service field updated successfully.',
            'data' => $field
        ]);
    }

    /**
     * Remove the specified service field.
     */
    public function destroy($id): JsonResponse
    {
        $field = ServiceField::find($id);

        if (!$field) {
            return response()->json([
                'status' => false,
                'message' => 'Service field not found.'
            ], 404);
        }

        $field->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service field deleted successfully.'
        ]);
    }
}
