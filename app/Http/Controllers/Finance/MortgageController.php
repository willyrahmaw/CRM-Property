<?php

namespace App\Http\Controllers\Finance;

use App\Enums\MortgageStatus;
use App\Enums\PaymentScheme;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreMortgageRequest;
use App\Http\Requests\Finance\UpdateMortgageRequest;
use App\Models\Booking;
use App\Models\Mortgage;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MortgageController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;

        $baseQuery = Mortgage::query()
            ->whereHas('booking', function ($q) use ($companyId) {
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
            });

        // Summary counts
        $counts = [
            'total' => (clone $baseQuery)->count(),
            'in_process' => (clone $baseQuery)->whereIn('status', [
                MortgageStatus::DRAFT->value,
                MortgageStatus::COLLECTING_DOCS->value,
                MortgageStatus::SUBMITTED->value,
                MortgageStatus::APPRAISAL->value,
            ])->count(),
            'sp3k_approved' => (clone $baseQuery)->where('status', MortgageStatus::APPROVED->value)->count(),
            'contract_signed' => (clone $baseQuery)->where('status', MortgageStatus::CONTRACT_SIGNED->value)->count(),
        ];

        $query = (clone $baseQuery)
            ->with([
                'booking.customer',
                'booking.propertyUnit.cluster.project',
                'booking.sales',
            ])
            ->latest('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bank')) {
            $query->where('bank_name', 'like', '%' . $request->bank . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($bq) use ($search) {
                        $bq->where('booking_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($cq) use ($search) {
                                $cq->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            })
                            ->orWhereHas('propertyUnit', function ($uq) use ($search) {
                                $uq->where('unit_number', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $mortgages = $query->paginate(15)->withQueryString();

        // Eligible bookings for new mortgage creation (Payment Scheme KPR and doesn't have mortgage yet)
        $eligibleBookings = Booking::query()
            ->where('payment_scheme', PaymentScheme::KPR)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereDoesntHave('mortgage')
            ->with(['customer', 'propertyUnit.cluster.project'])
            ->latest()
            ->get();

        return view('finance.mortgages.index', [
            'mortgages' => $mortgages,
            'statuses' => MortgageStatus::cases(),
            'counts' => $counts,
            'eligibleBookings' => $eligibleBookings,
            'filters' => $request->only(['status', 'bank', 'search']),
        ]);
    }

    public function create(Request $request): View
    {
        $companyId = $request->user()->company_id;

        $eligibleBookings = Booking::query()
            ->where('payment_scheme', PaymentScheme::KPR)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereDoesntHave('mortgage')
            ->with(['customer', 'propertyUnit.cluster.project', 'sales'])
            ->latest()
            ->get();

        $preselectedBookingId = $request->query('booking_id');

        return view('finance.mortgages.create', [
            'eligibleBookings' => $eligibleBookings,
            'preselectedBookingId' => $preselectedBookingId,
            'statuses' => MortgageStatus::cases(),
        ]);
    }

    public function edit(Mortgage $mortgage, Request $request): View
    {
        $companyId = $request->user()->company_id;

        if ($companyId && $mortgage->booking->company_id !== $companyId) {
            abort(403, 'Akses tidak sah ke data pengajuan KPR.');
        }

        $mortgage->load([
            'booking.customer',
            'booking.propertyUnit.cluster.project',
            'booking.sales',
            'booking.payments',
        ]);

        return view('finance.mortgages.edit', [
            'mortgage' => $mortgage,
            'statuses' => MortgageStatus::cases(),
        ]);
    }

    public function store(StoreMortgageRequest $request): RedirectResponse
    {
        $booking = Booking::findOrFail($request->booking_id);

        if ($request->user()->company_id && $booking->company_id !== $request->user()->company_id) {
            abort(403, 'Akses tidak sah ke data pemesanan perusahaan lain.');
        }

        $mortgage = DB::transaction(function () use ($request, $booking) {
            $mortgage = Mortgage::create([
                'booking_id' => $booking->id,
                'bank_name' => $request->bank_name,
                'submission_amount' => $request->submission_amount,
                'approved_amount' => $request->approved_amount,
                'tenor_years' => $request->tenor_years,
                'interest_rate' => $request->interest_rate,
                'estimated_installment' => $request->estimated_installment,
                'status' => $request->status,
                'application_date' => $request->application_date ?? now()->toDateString(),
                'appraisal_date' => $request->appraisal_date,
                'sp3k_date' => $request->sp3k_date,
                'contract_date' => $request->contract_date,
                'notes' => $request->notes,
            ]);

            $this->auditLogService->log(
                action: 'mortgage.created',
                entity: $mortgage,
                after: $mortgage->toArray()
            );

            return $mortgage;
        });

        return redirect()->route('finance.mortgages.index')
            ->with('success', "Pengajuan KPR untuk {$booking->customer->name} (Bank {$mortgage->bank_name}) berhasil didaftarkan.");
    }

    public function update(UpdateMortgageRequest $request, Mortgage $mortgage): RedirectResponse
    {
        $booking = $mortgage->booking;

        if ($request->user()->company_id && $booking->company_id !== $request->user()->company_id) {
            abort(403, 'Akses tidak sah ke data pengajuan KPR perusahaan lain.');
        }

        $validated = $request->validated();

        // Auto-fill dates if not manually provided upon status advance
        if ($validated['status'] === MortgageStatus::APPROVED->value && empty($validated['sp3k_date'])) {
            $validated['sp3k_date'] = now()->toDateString();
        }

        if ($validated['status'] === MortgageStatus::CONTRACT_SIGNED->value && empty($validated['contract_date'])) {
            $validated['contract_date'] = now()->toDateString();
        }

        $before = $mortgage->toArray();

        DB::transaction(function () use ($mortgage, $validated, $before) {
            $mortgage->update($validated);

            $this->auditLogService->log(
                action: 'mortgage.updated',
                entity: $mortgage,
                before: $before,
                after: $mortgage->fresh()->toArray()
            );
        });

        return redirect()->route('finance.mortgages.index')
            ->with('success', "Progres KPR Bank {$mortgage->bank_name} untuk pemesan {$booking->customer->name} berhasil diperbarui.");
    }
}
