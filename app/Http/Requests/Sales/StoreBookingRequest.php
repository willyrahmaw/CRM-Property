<?php

namespace App\Http\Requests\Sales;

use App\Enums\PaymentScheme;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'property_unit_id' => ['required', 'uuid', 'exists:property_units,id'],
            'customer_id' => ['nullable', 'uuid', 'exists:customers,id'],
            'lead_id' => ['nullable', 'uuid', 'exists:leads,id'],

            // If new customer without customer_id or lead_id
            'customer_name' => ['required_without_all:customer_id,lead_id', 'nullable', 'string', 'max:255'],
            'customer_gender' => ['nullable', Rule::enum(\App\Enums\Gender::class)],
            'customer_phone' => ['required_without_all:customer_id,lead_id', 'nullable', 'string', 'max:30'],
            'customer_nik' => ['nullable', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],

            'booking_fee' => ['required', 'numeric', 'min:1000000'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_scheme' => ['required', Rule::enum(PaymentScheme::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_unit_id.required' => 'Pilih unit properti yang akan di-booking.',
            'booking_fee.required' => 'Nominal booking fee / tanda jadi wajib diisi.',
            'booking_fee.min' => 'Nominal booking fee minimal Rp 1.000.000.',
            'customer_name.required_without_all' => 'Nama lengkap pembeli wajib diisi jika tidak memilih data konsumen yang sudah ada.',
            'customer_phone.required_without_all' => 'Nomor WhatsApp / telepon pembeli wajib diisi jika tidak memilih data konsumen yang sudah ada.',
            'customer_name.required_without' => 'Nama lengkap pembeli wajib diisi jika tidak memilih data konsumen yang sudah ada.',
            'customer_phone.required_without' => 'Nomor WhatsApp / telepon pembeli wajib diisi jika tidak memilih data konsumen yang sudah ada.',
        ];
    }
}
