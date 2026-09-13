<?php

namespace App\Http\Controllers\Sales;

use App\Enums\NegotiationApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreNegotiationRequest;
use App\Models\Lead;
use App\Models\Negotiation;
use App\Models\PropertyUnit;
use App\Models\User;
use App\Services\NegotiationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NegotiationController extends Controller
{
    public function __construct(
        protected NegotiationService $negotiationService
    ) {}

    public function index(Request $request): View
    {
        /** @var User|null $user */
        $user = $request->user();

        $query = Negotiation::with([
            'lead',
            'propertyUnit.cluster.project',
            'sales',
            'approver',
        ])->latest();

        // Multi-tenant company isolation
        if ($user && ! $user->isSuperAdmin() && $user->company_id) {
            $query->whereHas('lead', function ($lq) use ($user) {
                $lq->where('company_id', $user->company_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('lead', fn ($lq) => $lq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                  ->orWhereHas('propertyUnit', fn ($uq) => $uq->where('unit_number', 'like', "%{$search}%"))
                  ->orWhere('promo_description', 'like', "%{$search}%");
            });
        }

        $negotiations = $query->paginate(15)->withQueryString();

        $metricsQuery = Negotiation::query();
        if ($user && ! $user->isSuperAdmin() && $user->company_id) {
            $metricsQuery->whereHas('lead', function ($lq) use ($user) {
                $lq->where('company_id', $user->company_id);
            });
        }

        $metrics = [
            'total' => (clone $metricsQuery)->count(),
            'pending' => (clone $metricsQuery)->where('approval_status', NegotiationApprovalStatus::PENDING)->count(),
            'approved' => (clone $metricsQuery)->where('approval_status', NegotiationApprovalStatus::APPROVED)->count(),
            'total_discount' => (clone $metricsQuery)->where('approval_status', NegotiationApprovalStatus::APPROVED)->sum('discount_amount'),
        ];

        return view('sales.negotiations.index', [
            'negotiations' => $negotiations,
            'statuses' => NegotiationApprovalStatus::cases(),
            'currentStatus' => $request->status,
            'filters' => $request->only(['search', 'status']),
            'metrics' => $metrics,
        ]);
    }

    public function create(Request $request): View
    {
        $leads = Lead::orderBy('name')->get();
        $units = PropertyUnit::available()->with(['cluster.project', 'propertyType'])->get();

        $selectedLead = $request->filled('lead_id') ? Lead::find($request->lead_id) : null;
        $selectedUnit = $request->filled('unit_id') ? PropertyUnit::find($request->unit_id) : null;

        return view('sales.negotiations.create', [
            'leads' => $leads,
            'units' => $units,
            'selectedLead' => $selectedLead,
            'selectedUnit' => $selectedUnit,
        ]);
    }

    public function store(StoreNegotiationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $negotiation = $this->negotiationService->createNegotiation(
            $request->validated(),
            $user
        );

        return redirect()
            ->route('sales.negotiations.show', $negotiation)
            ->with('success', 'Pengajuan negosiasi & diskon berhasil dicatat.');
    }

    public function show(Negotiation $negotiation): View
    {
        $negotiation->load([
            'lead.assignedSales',
            'propertyUnit.cluster.project',
            'sales',
            'approver',
        ]);

        return view('sales.negotiations.show', [
            'negotiation' => $negotiation,
        ]);
    }

    public function approve(Request $request, Negotiation $negotiation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isManagerial()) {
            abort(403, 'Anda tidak memiliki hak akses managerial untuk menyetujui pengajuan diskon.');
        }

        $this->negotiationService->approveNegotiation(
            $negotiation,
            $user,
            $request->input('notes')
        );

        return redirect()
            ->route('sales.negotiations.show', $negotiation)
            ->with('success', 'Pengajuan diskon & penawaran negosiasi berhasil disetujui.');
    }

    public function reject(Request $request, Negotiation $negotiation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isManagerial()) {
            abort(403, 'Anda tidak memiliki hak akses managerial untuk menolak pengajuan diskon.');
        }

        $this->negotiationService->rejectNegotiation(
            $negotiation,
            $user,
            $request->input('reason')
        );

        return redirect()
            ->route('sales.negotiations.show', $negotiation)
            ->with('success', 'Pengajuan negosiasi & diskon telah ditolak.');
    }
}
