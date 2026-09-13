<?php

namespace App\Http\Requests\CRM;

use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateLeadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(LeadStatus::class)],
            'lost_reason' => [
                Rule::requiredIf(fn() => $this->input('status') === LeadStatus::LOST->value),
                'nullable',
                'string',
                'max:255',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'lost_reason.required' => 'Wajib memilih atau mengisi alasan pembatalan (Lost Reason) saat status menjadi Lost.',
        ];
    }
}
