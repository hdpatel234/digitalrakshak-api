<?php

namespace App\Http\Requests\Api\Client\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'integer'],
            'department' => ['required', 'integer'],
            'order' => ['nullable', 'integer'],
            'attachment' => ['nullable', 'array'],
            'attachment.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png,txt', 'max:2048'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png,txt', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'message.required' => 'Message is required.',
            'priority.required' => 'Priority is required.',
            'department.required' => 'Department is required.',
            'attachment.required' => 'Attachment is required.',
            'attachment.array' => 'Attachment must be an array.',
            'attachment.*.file' => 'Attachment must be a file.',
            'attachment.*.mimes' => 'Attachment must be a file of type: pdf,doc,docx,jpg,jpeg,png,txt.',
            'attachment.*.max' => 'Attachment must not be greater than 2048 kilobytes.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.file' => 'Attachment must be a file.',
            'attachments.*.mimes' => 'Attachment must be a file of type: pdf,doc,docx,jpg,jpeg,png,txt.',
            'attachments.*.max' => 'Attachment must not be greater than 2048 kilobytes.',
        ];
    }
}
