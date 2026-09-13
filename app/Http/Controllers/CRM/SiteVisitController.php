<?php

namespace App\Http\Controllers\CRM;

use App\Enums\SiteVisitStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreSiteVisitRequest;
use App\Models\Lead;
use App\Models\Project;
use App\Models\PropertyUnit;
use App\Models\SiteVisit;
use App\Models\User;
use App\Services\SiteVisitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteVisitController extends Controller
{
    public function __construct(
        protected SiteVisitService $siteVisitService
    ) {}

    public function index(Request $request): View
    {
        /** @var User|null $user */
        $user = $request->user();

        $query = SiteVisit::with([
            'lead',
            'sales',
            'project',
            'propertyUnit.cluster',
        ])->latest('visit_date');

        // Multi-tenant company isolation
        if ($user && ! $user->isSuperAdmin() && $user->company_id) {
            $query->whereHas('lead', function ($lq) use ($user) {
                $lq->where('company_id', $user->company_id);
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('lead', fn ($lq) => $lq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                  ->orWhereHas('project', fn ($pq) => $pq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('sales', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $siteVisits = $query->paginate(15)->withQueryString();

        $metricsQuery = SiteVisit::query();
        if ($user && ! $user->isSuperAdmin() && $user->company_id) {
            $metricsQuery->whereHas('lead', function ($lq) use ($user) {
                $lq->where('company_id', $user->company_id);
            });
        }

        $metrics = [
            'total' => (clone $metricsQuery)->count(),
            'scheduled' => (clone $metricsQuery)->where('status', SiteVisitStatus::SCHEDULED)->count(),
            'completed' => (clone $metricsQuery)->where('status', SiteVisitStatus::COMPLETED)->count(),
            'no_show' => (clone $metricsQuery)->where('status', SiteVisitStatus::NO_SHOW)->count(),
        ];

        return view('crm.site-visits.index', [
            'siteVisits' => $siteVisits,
            'statuses' => SiteVisitStatus::cases(),
            'currentStatus' => $request->status,
            'filters' => $request->only(['search', 'status']),
            'metrics' => $metrics,
        ]);
    }

    public function create(Request $request): View
    {
        /** @var User|null $user */
        $user = $request->user();

        $leadQuery = Lead::orderBy('name');
        $projectQuery = Project::orderBy('name');

        if ($user && ! $user->isSuperAdmin() && $user->company_id) {
            $leadQuery->where('company_id', $user->company_id);
            $projectQuery->where('company_id', $user->company_id);
        }

        $leads = $leadQuery->get();
        $projects = $projectQuery->get();
        $units = PropertyUnit::available()->with(['cluster.project'])->get();
        $salesAgents = User::whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedLead = $request->filled('lead_id') ? Lead::find($request->lead_id) : null;
        $selectedProject = $request->filled('project_id') ? Project::find($request->project_id) : null;

        return view('crm.site-visits.create', [
            'leads' => $leads,
            'projects' => $projects,
            'units' => $units,
            'salesAgents' => $salesAgents,
            'selectedLead' => $selectedLead,
            'selectedProject' => $selectedProject,
        ]);
    }

    public function store(StoreSiteVisitRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $siteVisit = $this->siteVisitService->scheduleVisit(
            $request->validated(),
            $user
        );

        return redirect()
            ->route('crm.site-visits.index')
            ->with('success', 'Jadwal kunjungan lokasi (Site Visit) berhasil didaftarkan untuk ' . $siteVisit->lead->name . '.');
    }

    public function updateStatus(Request $request, SiteVisit $siteVisit): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:completed,cancelled,no_show'],
            'result' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = SiteVisitStatus::from($validated['status']);

        $this->siteVisitService->updateStatus(
            $siteVisit,
            $status,
            $validated['result'] ?? null,
            $validated['notes'] ?? null
        );

        return redirect()
            ->route('crm.site-visits.index')
            ->with('success', 'Status kunjungan lokasi berhasil diperbarui menjadi ' . $status->label() . '.');
    }
}
