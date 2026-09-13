<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'uuid', 'exists:leads,id'],
            'activity_type' => ['required', 'string', 'in:whatsapp,call,meeting,site_visit,email,note'],
            'activity_date' => ['required', 'date'],
            'result' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_follow_up_date' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'activity_type.required' => 'Jenis aktivitas follow-up wajib dipilih.',
            'activity_date.required' => 'Tanggal & waktu aktivitas wajib diisi.',
            'next_follow_up_date.after' => 'Jadwal follow-up berikutnya harus berupa waktu yang akan datang.',
        ];
    }
}
