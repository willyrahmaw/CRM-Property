<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreClusterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Proyek induk wajib dipilih.',
            'project_id.exists' => 'Proyek yang dipilih tidak valid.',
            'name.required' => 'Nama cluster wajib diisi.',
            'photos.max' => 'Maksimal 10 foto cluster dapat diunggah sekaligus.',
            'photos.*.image' => 'Setiap berkas harus berupa format gambar (JPG, PNG, WEBP).',
            'photos.*.max' => 'Ukuran setiap foto tidak boleh melebihi 5MB.',
        ];
    }
}
