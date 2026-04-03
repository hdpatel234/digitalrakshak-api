<?php

namespace App\Http\Requests\Api\Client\Invitation;

use App\Http\Requests\Api\BaseRequest;

class UpdateCandidateInvitationByTokenRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();
        if (array_is_list($input) && isset($input[0]) && is_array($input[0])) {
            $input = $input[0];
        }

        $this->merge([
            'candidate_details' => is_array($input['candidate_details'] ?? null) ? $input['candidate_details'] : [],
            'fields' => is_array($input['fields'] ?? null) ? $input['fields'] : [],
        ]);
    }

    public function rules(): array
    {
        return [
            'candidate_details' => ['required', 'array'],
            'candidate_details.first_name' => ['required', 'string', 'max:100'],
            'candidate_details.last_name' => ['nullable', 'string', 'max:100'],
            'candidate_details.email' => ['required', 'email', 'max:191'],
            'candidate_details.phone' => ['nullable', 'string', 'max:20'],
            'candidate_details.address' => ['nullable', 'string', 'max:500'],
            'candidate_details.country_id' => ['nullable', 'integer'],
            'candidate_details.state_id' => ['nullable', 'integer'],
            'candidate_details.city_id' => ['nullable', 'integer'],
            'candidate_details.pincode' => ['nullable', 'string', 'max:20'],
            'fields' => ['required', 'array'],
            'fields.*.field_id' => ['required', 'integer', 'distinct'],
            'fields.*.value' => ['nullable'],
        ];
    }
}
