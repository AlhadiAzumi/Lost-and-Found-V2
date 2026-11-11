<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'satpam'], true);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'rejection_reason' => [
                Rule::requiredIf($this->input('status') === 'rejected'),
                Rule::prohibitedIf($this->input('status') === 'approved'),
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Alasan penolakan laporan wajib diisi.',
        ];
    }
}
