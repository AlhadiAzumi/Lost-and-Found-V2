<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'security_answer' => ['required', 'string', 'max:500'],
            'proof_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'security_answer.required' => 'Jawaban pertanyaan keamanan wajib diisi.',
            'proof_image.mimes' => 'Bukti harus berupa JPG, PNG, WebP, atau PDF.',
            'proof_image.max' => 'Ukuran bukti maksimal 2 MB.',
        ];
    }
}
