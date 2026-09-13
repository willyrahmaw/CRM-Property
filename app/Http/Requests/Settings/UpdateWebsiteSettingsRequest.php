<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isCompanyOwner() || $user->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'site_title' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about_text' => ['nullable', 'string', 'max:3000'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'operational_hours' => ['nullable', 'string', 'max:100'],
            'google_maps_url' => ['nullable', 'string', 'max:1000'],
            'announcement_active' => ['nullable', 'boolean'],
            'announcement_text' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:100'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:100'],
            'meta_title' => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'facebook_pixel_id' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,webp,svg', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'site_title.required' => 'Judul website/portal resmi perusahaan wajib diisi.',
            'email.email' => 'Format email resmi tidak valid.',
            'logo.image' => 'File logo harus berupa gambar (PNG, JPG, WEBP, atau SVG).',
            'logo.max' => 'Ukuran file logo maksimal 2MB.',
            'favicon.max' => 'Ukuran file favicon maksimal 1MB.',
        ];
    }
}
