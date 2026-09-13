<?php

namespace App\Http\Requests\CRM;

use App\Enums\Gender;
use App\Enums\LeadSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'source' => ['required', Rule::enum(LeadSource::class)],
            'campaign' => ['nullable', 'string', 'max:255'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'gte:budget_min'],
            'interested_project_id' => ['nullable', 'uuid', 'exists:projects,id'],
            'property_type_interest' => ['nullable', 'string', 'max:255'],
            'assigned_sales_id' => ['nullable', 'uuid', 'exists:users,id'],
            'purchase_target_days' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama prospek / lead wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'budget_max.gte' => 'Budget maksimum tidak boleh lebih kecil dari budget minimum.',
        ];
    }
}
