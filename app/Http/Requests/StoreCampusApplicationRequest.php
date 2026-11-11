<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampusApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kampus wajib diisi.',
            'email.required' => 'Email pemohon wajib diisi.',
            'email.email' => 'Format email pemohon tidak valid.',
        ];
    }
}
