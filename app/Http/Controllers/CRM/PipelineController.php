<?php

namespace App\Http\Controllers\CRM;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\UpdateLeadStatusRequest;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PipelineController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function index(): View
    {
        $stages = [
            LeadStatus::NEW,
            LeadStatus::CONTACTED,
            LeadStatus::QUALIFIED,
            LeadStatus::SITE_VISIT,
            LeadStatus::NEGOTIATION,
            LeadStatus::BOOKING,
            LeadStatus::WON,
            LeadStatus::LOST,
        ];

        $leads = Lead::with(['assignedSales', 'interestedProject'])
            ->latest('updated_at')
            ->get()
            ->groupBy('status.value');

        return view('crm.pipeline.index', [
            'stages' => $stages,
            'leadsByStage' => $leads,
        ]);
    }

    public function updateStage(UpdateLeadStatusRequest $request, Lead $lead): JsonResponse
    {
        $newStatus = LeadStatus::from($request->validated('status'));
        $lostReason = $request->validated('lost_reason');
        $notes = $request->validated('notes');

        $this->leadService->updateStatus($lead, $newStatus, $lostReason, $notes);

        return response()->json([
            'success' => true,
            'message' => "Lead {$lead->name} berhasil dipindahkan ke tahapan {$newStatus->label()}.",
            'lead' => $lead->fresh(['assignedSales']),
        ]);
    }
}
