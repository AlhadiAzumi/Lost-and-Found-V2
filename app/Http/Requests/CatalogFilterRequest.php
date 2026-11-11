<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['lost', 'found'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'location' => ['nullable', 'string', 'max:150'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sort' => ['nullable', Rule::in(['terbaru', 'terlama'])],
        ];
    }
}
