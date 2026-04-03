<?php

namespace App\Http\Requests\Api\Client\Invitation;

use App\Http\Requests\Api\BaseRequest;

class ParseResumeRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:10240'],
            'prompt_code' => ['nullable', 'string', 'max:100'],
        ];
    }
}
