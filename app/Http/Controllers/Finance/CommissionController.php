<?php

namespace App\Http\Controllers\Finance;

use App\Enums\CommissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $query = Commission::with(['booking.customer', 'booking.propertyUnit', 'user', 'approver'])->latest();

        // If sales agent, only view own commission
        if ($user && $user->isSalesAgent()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->paginate(15)->withQueryString();

        $statsQuery = Commission::query();
        if ($user && $user->isSalesAgent()) {
            $statsQuery->where('user_id', $user->id);
        }

        $totalPending = (clone $statsQuery)->where('status', CommissionStatus::PENDING)->sum('amount');
        $totalApproved = (clone $statsQuery)->where('status', CommissionStatus::APPROVED)->sum('amount');
        $totalPaid = (clone $statsQuery)->where('status', CommissionStatus::PAID)->sum('amount');

        return view('finance.commissions.index', [
            'commissions' => $commissions,
            'statuses' => CommissionStatus::cases(),
            'totalPending' => $totalPending,
            'totalApproved' => $totalApproved,
            'totalPaid' => $totalPaid,
            'filters' => $request->all(),
        ]);
    }

    public function approve(Request $request, Commission $commission): RedirectResponse
    {
        $this->commissionService->approve($commission, $request->user());

        return back()->with('success', 'Komisi berhasil disetujui untuk pencairan.');
    }

    public function markAsPaid(Request $request, Commission $commission): RedirectResponse
    {
        $this->commissionService->markAsPaid($commission, $request->user());

        return back()->with('success', 'Komisi telah resmi dibayarkan kepada penerima.');
    }

    public function generate(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'rate' => ['nullable', 'numeric', 'min:0.1', 'max:20'],
            'sales_share' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'team_leader_share' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $rate = isset($validated['rate']) ? (float) $validated['rate'] : 2.5;
        $salesShare = isset($validated['sales_share']) ? (float) $validated['sales_share'] : 60.0;
        $tlShare = isset($validated['team_leader_share']) ? (float) $validated['team_leader_share'] : 20.0;

        try {
            $this->commissionService->calculateAndGenerate(
                booking: $booking,
                totalCommissionRate: $rate,
                salesShare: $salesShare,
                teamLeaderShare: $tlShare,
                force: true
            );

            return back()->with('success', 'Komisi penjualan berhasil dihitung dan diterbitkan.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
