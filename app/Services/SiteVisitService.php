<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\SiteVisitStatus;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiteVisitService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Schedule a new property site visit.
     */
    public function scheduleVisit(array $data, User $sales): SiteVisit
    {
        return DB::transaction(function () use ($data, $sales) {
            $siteVisit = SiteVisit::create([
                'lead_id' => $data['lead_id'],
                'user_id' => $data['user_id'] ?? $sales->id,
                'project_id' => $data['project_id'],
                'property_unit_id' => $data['property_unit_id'] ?? null,
                'visit_date' => $data['visit_date'],
                'status' => SiteVisitStatus::SCHEDULED,
                'notes' => $data['notes'] ?? null,
            ]);

            // Advance lead to SITE_VISIT if still in earlier stage
            $lead = Lead::find($data['lead_id']);
            if ($lead && in_array($lead->status, [LeadStatus::NEW, LeadStatus::CONTACTED, LeadStatus::QUALIFIED])) {
                $lead->update(['status' => LeadStatus::SITE_VISIT]);
            }

            // Record as activity
            LeadActivity::create([
                'lead_id' => $siteVisit->lead_id,
                'user_id' => $sales->id,
                'activity_type' => 'site_visit',
                'activity_date' => $siteVisit->visit_date,
                'result' => 'Terjadwal: Survei lokasi di ' . $siteVisit->project->name,
                'notes' => $siteVisit->notes,
            ]);

            $this->auditLogService->log(
                action: 'schedule_site_visit',
                entity: 'SiteVisit',
                before: null,
                after: [
                    'lead_id' => $siteVisit->lead_id,
                    'visit_date' => $siteVisit->visit_date->toIso8601String(),
                    'status' => $siteVisit->status->value,
                ],
                entityId: $siteVisit->id
            );

            return $siteVisit;
        });
    }

    /**
     * Update visit status (completed, cancelled, no-show) with result.
     */
    public function updateStatus(SiteVisit $siteVisit, SiteVisitStatus $status, ?string $result = null, ?string $notes = null): SiteVisit
    {
        return DB::transaction(function () use ($siteVisit, $status, $result, $notes) {
            $beforeStatus = $siteVisit->status->value;

            $siteVisit->update([
                'status' => $status,
                'result' => $result ?? $siteVisit->result,
                'notes' => $notes ?? $siteVisit->notes,
            ]);

            $lead = $siteVisit->lead;

            // If visit completed, increase lead score by 20 according to scoring rules
            if ($status === SiteVisitStatus::COMPLETED && $lead) {
                $lead->increment('score', 20);
            }

            LeadActivity::create([
                'lead_id' => $siteVisit->lead_id,
                'user_id' => Auth::id() ?? $siteVisit->user_id,
                'activity_type' => 'site_visit',
                'activity_date' => now(),
                'result' => 'Status kunjungan: ' . $status->label() . ($result ? " — {$result}" : ''),
                'notes' => $notes,
            ]);

            $this->auditLogService->log(
                action: 'update_site_visit_status',
                entity: 'SiteVisit',
                before: ['status' => $beforeStatus],
                after: ['status' => $status->value, 'result' => $result],
                entityId: $siteVisit->id
            );

            return $siteVisit;
        });
    }
}
