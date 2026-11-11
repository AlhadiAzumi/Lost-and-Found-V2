<?php

namespace App\Http\Requests;

use App\Support\CampusContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $isFound = $this->input('type') === 'found';

        return [
            'type' => ['required', Rule::in(['lost', 'found'])],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'location_id' => [
                'nullable',
                'integer',
                Rule::exists('locations', 'id')->where('campus_id', app(CampusContext::class)->id()),
            ],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:3000'],
            'location' => ['required', 'string', 'max:150'],
            'storage_location' => [$isFound ? 'required' : 'prohibited', 'nullable', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'date_event' => ['required', 'date', 'before_or_equal:today'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_sensitive' => ['sometimes', 'boolean'],
            'security_question' => [$isFound ? 'required' : 'prohibited', 'nullable', 'string', 'max:500'],
            'security_answer' => [$isFound ? 'required' : 'prohibited', 'nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Jenis laporan wajib dipilih.',
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia.',
            'title.required' => 'Nama barang wajib diisi.',
            'description.required' => 'Deskripsi barang wajib diisi.',
            'location.required' => 'Lokasi kejadian wajib diisi.',
            'storage_location.required' => 'Lokasi penitipan wajib diisi untuk barang temuan.',
            'security_question.required' => 'Pertanyaan keamanan wajib diisi untuk barang temuan.',
            'security_answer.required' => 'Jawaban keamanan wajib diisi untuk barang temuan.',
            'image.max' => 'Ukuran foto maksimal 2 MB.',
            'image.image' => 'Berkas foto harus berupa gambar.',
            'date_event.before_or_equal' => 'Tanggal kejadian tidak boleh melewati hari ini.',
        ];
    }
}
