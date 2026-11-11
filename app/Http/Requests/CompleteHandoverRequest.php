<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'satpam'], true);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode serah terima wajib diisi.',
        ];
    }
}
