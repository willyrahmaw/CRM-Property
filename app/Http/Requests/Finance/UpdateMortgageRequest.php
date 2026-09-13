<?php

namespace App\Http\Requests\Finance;

use App\Enums\MortgageStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateMortgageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->isFinance() || Auth::user()->isManagerial());
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(MortgageStatus::class)],
            'bank_name' => ['required', 'string', 'max:100'],
            'submission_amount' => ['required', 'numeric', 'min:0'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'tenor_years' => ['required', 'integer', 'min:1', 'max:40'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'estimated_installment' => ['nullable', 'numeric', 'min:0'],
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
            'status.required' => 'Status proses KPR wajib dipilih.',
            'bank_name.required' => 'Nama bank rekanan penyalur KPR wajib diisi.',
            'submission_amount.required' => 'Plafon pengajuan KPR wajib diisi.',
            'tenor_years.required' => 'Jangka waktu tenor KPR wajib ditentukan.',
        ];
    }
}
