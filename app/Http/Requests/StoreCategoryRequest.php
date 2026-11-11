<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'satpam'], true);
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:120', Rule::unique(Category::class, 'slug')->ignore($category)],
            'icon' => ['nullable', 'string', 'max:50'],
        ];
    }
}
