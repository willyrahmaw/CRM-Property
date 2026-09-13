<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreNegotiationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'uuid', 'exists:leads,id'],
            'property_unit_id' => ['required', 'uuid', 'exists:property_units,id'],
            'customer_offer_price' => ['nullable', 'numeric', 'min:10000000'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'promo_description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'lead_id.required' => 'Prospek konsumen (Lead) wajib dipilih.',
            'property_unit_id.required' => 'Unit properti yang dinegosiasikan wajib dipilih.',
            'customer_offer_price.min' => 'Harga penawaran konsumen tidak valid.',
            'discount_amount.min' => 'Nilai diskon tidak boleh negatif.',
        ];
    }
}
