<?php

namespace App\Http\Requests\Api\Client\Invitation;

use App\Http\Requests\Api\BaseRequest;

class StoreCandidateInvitationRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $routeCandidate = $this->route('candidate');
        $candidateIds = $this->input('candidate_ids', $this->input('candiate_ids'));
        $packageIds = $this->input('package_ids', $this->input('packge_ids', $this->input('package_id')));

        if (($candidateIds === null || $candidateIds === []) && $routeCandidate !== null) {
            $candidateIds = [is_object($routeCandidate) ? ($routeCandidate->id ?? null) : $routeCandidate];
        }

        if ($candidateIds !== null && !is_array($candidateIds)) {
            $candidateIds = [$candidateIds];
        }

        if ($packageIds !== null && !is_array($packageIds)) {
            $packageIds = [$packageIds];
        }

        $this->merge([
            'candidate_ids' => $candidateIds,
            'package_ids' => $packageIds,
        ]);
    }

    public function rules(): array
    {
        return [
            'candidate_ids' => ['required', 'array', 'min:1'],
            'candidate_ids.*' => ['required', 'integer', 'distinct'],
            'package_ids' => ['required', 'array', 'min:1'],
            'package_ids.*' => ['required', 'integer', 'distinct'],
            'invitation_type' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'expires_at' => ['nullable', 'date'],
            'form_data' => ['nullable', 'array'],
        ];
    }
}
