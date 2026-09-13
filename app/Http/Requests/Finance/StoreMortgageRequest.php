<?php

namespace App\Http\Requests\Finance;

use App\Enums\MortgageStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreMortgageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->isFinance() || Auth::user()->isManagerial());
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'uuid', 'exists:bookings,id', 'unique:mortgages,booking_id'],
            'bank_name' => ['required', 'string', 'max:100'],
            'submission_amount' => ['required', 'numeric', 'min:1000000'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'tenor_years' => ['required', 'integer', 'min:1', 'max:40'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'estimated_installment' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(MortgageStatus::class)],
            'application_date' => ['nullable', 'date'],
            'appraisal_date' => ['nullable', 'date'],
            'sp3k_date' => ['nullable', 'date'],
            'contract_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Pilih transaksi pemesanan unit terkait.',
            'booking_id.unique' => 'Pengajuan KPR untuk transaksi ini sudah terdaftar.',
            'bank_name.required' => 'Nama bank rekanan wajib diisi.',
            'submission_amount.required' => 'Plafon pengajuan KPR wajib diisi.',
            'tenor_years.required' => 'Tenor tahun wajib ditentukan.',
        ];
    }
}
