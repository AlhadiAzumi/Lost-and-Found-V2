<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Support\CampusContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'satpam'], true);
    }

    public function rules(): array
    {
        $location = $this->route('location');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique(Location::class, 'name')
                    ->where('campus_id', app(CampusContext::class)->id())
                    ->ignore($location),
            ],
        ];
    }
}
