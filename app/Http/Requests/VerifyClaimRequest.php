<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'satpam'], true);
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'admin_note' => [
                Rule::requiredIf($this->input('action') === 'reject'),
                Rule::prohibitedIf($this->input('action') === 'approve'),
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_note.required' => 'Alasan penolakan klaim wajib diisi.',
        ];
    }
}
