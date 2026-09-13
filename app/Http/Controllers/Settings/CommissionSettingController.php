<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateCommissionSettingsRequest;
use App\Models\Company;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionSettingController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Display the commission settings management screen for Company Owner.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $company = $user->company ?? Company::firstOrFail();

        $defaults = [
            'total_rate' => 2.5,
            'sales_share' => 60.0,
            'team_leader_share' => 20.0,
            'agency_share' => 20.0,
            'auto_generate_on_booking_fee' => true,
            'disbursement_policy' => 'after_booking_fee',
            'terms_and_conditions' => "1. Fotokopi KTP & NPWP Agen Sales aktif terdaftar di sistem.\n2. Pembayaran tanda jadi (Booking Fee) telah diverifikasi lunas oleh Finance.\n3. Berkas Surat Pesanan Properti (SPP) telah ditandatangani sah oleh konsumen dan disetujui manajemen.",
        ];

        $currentSettings = array_merge($defaults, $company->commission_settings ?? []);

        return view('settings.commissions.index', [
            'company' => $company,
            'settings' => $currentSettings,
        ]);
    }

    /**
     * Update the company commission configuration.
     */
    public function update(UpdateCommissionSettingsRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $company = $user->company ?? Company::firstOrFail();

        $validated = $request->validated();
        $validated['auto_generate_on_booking_fee'] = $request->boolean('auto_generate_on_booking_fee');
        $validated['total_rate'] = (float) $validated['total_rate'];
        $validated['sales_share'] = (float) $validated['sales_share'];
        $validated['team_leader_share'] = (float) $validated['team_leader_share'];
        $validated['agency_share'] = (float) $validated['agency_share'];

        $oldSettings = $company->commission_settings ?? [];

        $company->commission_settings = $validated;
        $company->save();

        $this->auditLogService->log(
            action: 'commission_settings_updated',
            entity: $company,
            before: $oldSettings,
            after: $validated
        );

        return redirect()
            ->route('settings.commissions.index')
            ->with('success', 'Skema dan kebijakan komisi penjualan perusahaan berhasil diperbarui oleh Owner.');
    }
}
