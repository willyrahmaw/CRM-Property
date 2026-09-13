<?php

namespace App\Http\Requests\Inventory;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'developer_name' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'status' => ['required', new Enum(ProjectStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama proyek properti wajib diisi.',
            'city.required' => 'Kota/wilayah lokasi proyek wajib diisi.',
        ];
    }
}
