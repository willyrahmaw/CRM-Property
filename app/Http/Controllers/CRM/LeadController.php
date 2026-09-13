<?php

namespace App\Http\Controllers\CRM;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreLeadRequest;
use App\Http\Requests\CRM\UpdateLeadStatusRequest;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'temperature', 'sales_id', 'project_id']);
        $leads = $this->leadService->paginate($filters, 15);

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $salesAgents = User::whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('crm.leads.index', [
            'leads' => $leads,
            'filters' => $filters,
            'projects' => $projects,
            'salesAgents' => $salesAgents,
            'statuses' => LeadStatus::cases(),
            'temperatures' => LeadTemperature::cases(),
        ]);
    }

    public function create(): View
    {
        $projects = Project::orderBy('name')->get();
        $salesAgents = User::whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('crm.leads.create', [
            'projects' => $projects,
            'salesAgents' => $salesAgents,
            'sources' => LeadSource::cases(),
        ]);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $lead = $this->leadService->create($request->validated());

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', "Prospek {$lead->name} berhasil ditambahkan dengan kode {$lead->code}.");
    }

    public function show(Lead $lead): View
    {
        $lead->load([
            'assignedSales',
            'interestedProject',
            'activities.user',
            'siteVisits.sales',
            'siteVisits.project',
            'siteVisits.propertyUnit',
            'negotiations.propertyUnit.cluster.project',
            'negotiations.sales',
            'customer',
        ]);

        return view('crm.leads.show', [
            'lead' => $lead,
            'statuses' => LeadStatus::cases(),
        ]);
    }

    public function updateStatus(UpdateLeadStatusRequest $request, Lead $lead): RedirectResponse
    {
        $newStatus = LeadStatus::from($request->validated('status'));
        $lostReason = $request->validated('lost_reason');
        $notes = $request->validated('notes');

        $this->leadService->updateStatus($lead, $newStatus, $lostReason, $notes);

        return back()->with('success', "Status lead berhasil diperbarui menjadi {$newStatus->label()}.");
    }
}
