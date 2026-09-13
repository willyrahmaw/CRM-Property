<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePropertyUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'cluster_id' => ['required', 'uuid', 'exists:clusters,id'],
            'property_type_id' => ['nullable', 'uuid', 'exists:property_types,id'],
            'unit_number' => ['required', 'string', 'max:50'],
            'block' => ['nullable', 'string', 'max:20'],
            'land_area' => ['required', 'numeric', 'min:1'],
            'dimension' => ['nullable', 'string', 'max:50'],
            'building_area' => ['required', 'numeric', 'min:1'],
            'bedrooms' => ['required', 'integer', 'min:1'],
            'bathrooms' => ['required', 'integer', 'min:1'],
            'floors' => ['required', 'integer', 'min:1'],
            'carports' => ['nullable', 'integer', 'min:0', 'max:10'],
            'direction' => ['nullable', 'string', 'max:50'],
            'electricity' => ['nullable', 'string', 'max:50'],
            'water_source' => ['nullable', 'string', 'max:100'],
            'certificate_type' => ['nullable', 'string', 'max:50'],
            'building_specs' => ['nullable', 'array'],
            'selling_price' => ['required', 'numeric', 'min:1000000'],
            'base_price' => ['nullable', 'numeric'],
            'promo' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'cluster_id.required' => 'Cluster properti wajib dipilih.',
            'unit_number.required' => 'Nomor unit wajib diisi.',
            'land_area.required' => 'Luas tanah (LT) wajib diisi.',
            'building_area.required' => 'Luas bangunan (LB) wajib diisi.',
            'selling_price.required' => 'Harga jual unit wajib diisi.',
        ];
    }
}
