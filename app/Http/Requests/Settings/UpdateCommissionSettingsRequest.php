<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCommissionSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = $this->user();
        return $user && ($user->isCompanyOwner() || $user->isSuperAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'total_rate' => ['required', 'numeric', 'min:0.1', 'max:25'],
            'sales_share' => ['required', 'numeric', 'min:1', 'max:100'],
            'team_leader_share' => ['required', 'numeric', 'min:0', 'max:100'],
            'agency_share' => ['required', 'numeric', 'min:0', 'max:100'],
            'auto_generate_on_booking_fee' => ['nullable', 'boolean'],
            'disbursement_policy' => ['required', 'string', 'in:after_booking_fee,after_dp_paid,after_akad'],
            'terms_and_conditions' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $sales = (float) $this->input('sales_share', 0);
            $tl = (float) $this->input('team_leader_share', 0);
            $agency = (float) $this->input('agency_share', 0);

            $totalShare = round($sales + $tl + $agency, 2);

            if ($totalShare !== 100.0) {
                $validator->errors()->add(
                    'shares_total',
                    "Total persentase pembagian komisi harus tepat 100%. Saat ini: {$totalShare}% (Sales {$sales}% + Team Leader {$tl}% + Kantor {$agency}%)."
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'total_rate.required' => 'Tarif total komisi properti wajib diisi.',
            'total_rate.numeric' => 'Tarif total komisi harus berupa angka.',
            'total_rate.min' => 'Tarif total komisi minimal 0.1%.',
            'total_rate.max' => 'Tarif total komisi maksimal 25%.',
            'sales_share.required' => 'Porsi komisi Sales Closing wajib diisi.',
            'team_leader_share.required' => 'Porsi komisi Team Leader wajib diisi.',
            'agency_share.required' => 'Porsi cadangan kantor / agency wajib diisi.',
            'disbursement_policy.required' => 'Kebijakan waktu pencairan komisi wajib dipilih.',
            'disbursement_policy.in' => 'Pilihan kebijakan waktu pencairan tidak valid.',
        ];
    }
}
