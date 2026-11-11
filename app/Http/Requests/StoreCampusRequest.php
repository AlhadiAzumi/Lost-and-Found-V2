<?php

namespace App\Http\Requests;

use App\Models\Campus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampusRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'lowercase',
                'alpha_dash',
                'max:100',
                Rule::unique(Campus::class, 'slug'),
            ],
        ];
    }
}
