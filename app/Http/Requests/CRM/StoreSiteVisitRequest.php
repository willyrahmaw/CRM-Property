<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSiteVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'uuid', 'exists:leads,id'],
            'project_id' => ['required', 'uuid', 'exists:projects,id'],
            'property_unit_id' => ['nullable', 'uuid', 'exists:property_units,id'],
            'visit_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'lead_id.required' => 'Prospek / Lead wajib dipilih.',
            'project_id.required' => 'Project tujuan kunjungan wajib dipilih.',
            'visit_date.required' => 'Waktu dan tanggal survei lokasi wajib ditentukan.',
            'visit_date.after' => 'Jadwal survei lokasi harus berupa waktu yang akan datang.',
        ];
    }
}
