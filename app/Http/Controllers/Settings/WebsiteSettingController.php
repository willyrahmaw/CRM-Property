<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateWebsiteSettingsRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    /**
     * Display the website settings management screen for Owner.
     */
    public function index(Request $request): View
    {
        $company = $request->user()->company ?? Company::firstOrFail();

        $defaults = [
            'site_title' => $company->name,
            'tagline' => 'Luxury Property & Living Redefined',
            'about_text' => 'Pengembang properti terdepan yang menghadirkan hunian mewah bernilai investasi tinggi dengan fasilitas kawasan terpadu.',
            'email' => $company->email ?? 'info@propflow.local',
            'phone' => $company->phone ?? '021-88997700',
            'whatsapp' => '081199887766',
            'address' => $company->address ?? 'Jl. Boulevard Utama Grand Emerald No. 1, Tangerang',
            'operational_hours' => 'Senin - Minggu: 08:30 - 18:00 WIB',
            'google_maps_url' => 'https://maps.google.com',
            'announcement_active' => true,
            'announcement_text' => 'Promo Spesial: Subsidi DP 10%, Bebas Biaya KPR & AJB s/d Akhir Periode!',
            'instagram' => '@propflow.residence',
            'facebook' => 'https://facebook.com/propflow.official',
            'youtube' => 'https://youtube.com/@propflowproperty',
            'tiktok' => '@propflow.luxury',
            'meta_title' => $company->name . ' — Official Luxury Property Developer',
            'meta_description' => 'Temukan hunian kaveling & rumah mewah eksklusif dari ' . $company->name . ' dengan fasilitas modern & KPR bunga ringan.',
            'meta_keywords' => 'rumah mewah, kaveling eksklusif, perumahan bsd, kpr properti',
            'google_analytics_id' => 'G-PROPFLOW01',
            'facebook_pixel_id' => '',
        ];

        $currentSettings = array_merge($defaults, $company->website_settings ?? []);

        return view('settings.website.index', [
            'company' => $company,
            'settings' => $currentSettings,
        ]);
    }

    /**
     * Update the company website settings.
     */
    public function update(UpdateWebsiteSettingsRequest $request): RedirectResponse
    {
        $company = $request->user()->company ?? Company::firstOrFail();
        $validated = $request->validated();

        $existingSettings = $company->website_settings ?? [];

        // Process Logo Upload
        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $logoPath = $request->file('logo')->store('company/branding', 'public');
            $company->logo_path = $logoPath;
            $validated['logo_path'] = $logoPath;
        } else {
            $validated['logo_path'] = $existingSettings['logo_path'] ?? $company->logo_path;
        }

        // Process Favicon Upload
        if ($request->hasFile('favicon')) {
            if (!empty($existingSettings['favicon_path']) && Storage::disk('public')->exists($existingSettings['favicon_path'])) {
                Storage::disk('public')->delete($existingSettings['favicon_path']);
            }
            $faviconPath = $request->file('favicon')->store('company/branding', 'public');
            $validated['favicon_path'] = $faviconPath;
        } else {
            $validated['favicon_path'] = $existingSettings['favicon_path'] ?? null;
        }

        // Remove UploadedFile objects so they are not encoded into JSON
        unset($validated['logo'], $validated['favicon']);

        // Normalize Boolean
        $validated['announcement_active'] = $request->boolean('announcement_active');

        // Update Base Company info & JSON website settings
        $company->email = $validated['email'] ?? $company->email;
        $company->phone = $validated['phone'] ?? $company->phone;
        $company->address = $validated['address'] ?? $company->address;
        $company->website_settings = array_merge($existingSettings, $validated);
        $company->save();

        return redirect()
            ->route('settings.website.index')
            ->with('success', 'Pengaturan website dan profil publik perusahaan berhasil diperbarui.');
    }
}
