<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampusStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin'
            && filled(config('campus.platform_admin_email'))
            && hash_equals(
                mb_strtolower(config('campus.platform_admin_email')),
                mb_strtolower($this->user()->email)
            );
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
        ];
    }
}
