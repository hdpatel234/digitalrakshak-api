<?php

namespace App\Http\Requests\Api\Client\Candidate;

use App\Http\Requests\Api\BaseRequest;

class StoreCandidateImportRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,xlsx', 'max:10240'],
        ];
    }
}
