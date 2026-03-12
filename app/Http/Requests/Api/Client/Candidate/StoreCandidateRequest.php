<?php

namespace App\Http\Requests\Api\Client\Candidate;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCandidateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = (int) (auth('api')->user()?->client_id ?? auth()->user()?->client_id ?? 0);

        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('candidates', 'email')->where(
                    fn ($query) => $query->where('client_id', $clientId)
                ),
            ],
            'dialCode' => ['nullable', 'string', 'max:10'],
            'phoneNumber' => ['nullable', 'string', 'max:25'],
            'country' => ['nullable', 'integer'],
            'state' => ['nullable', 'integer'],
            'city' => ['nullable', 'integer'],
            'address' => ['nullable', 'string', 'max:1000'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'managerEmails' => ['nullable', 'array'],
            'managerEmails.*' => ['nullable', 'email', 'max:255'],
            'img' => ['nullable'],
            'tags' => ['nullable', 'array'],
            'send_invite' => ['nullable', 'boolean'],
            'ip_address' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email already exists for your client account.',
        ];
    }
}
