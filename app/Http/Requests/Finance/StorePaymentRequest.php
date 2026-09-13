<?php

namespace App\Http\Requests\Finance;

use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'uuid', 'exists:bookings,id'],
            'payment_type' => ['required', Rule::enum(PaymentType::class)],
            'amount' => ['required', 'numeric', 'min:10000'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Pilih transaksi booking terkait.',
            'amount.required' => 'Nominal pembayaran wajib diisi.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'proof_file.max' => 'Ukuran bukti transfer maksimal 5MB.',
        ];
    }
}
