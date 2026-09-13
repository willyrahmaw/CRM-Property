<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\LeadAssignment\LeadAssignmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LeadService
{
    public function __construct(
        protected LeadAssignmentService $assignmentService,
        protected LeadScoringService $scoringService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Generate unique sequential code for new leads.
     */
    public function generateLeadCode(): string
    {
        $prefix = 'LD-' . date('Ym') . '-';
        $latest = Lead::withoutGlobalScopes()
            ->where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Create a new lead with assignment and initial score.
     */
    public function create(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->generateLeadCode();
            $lead = Lead::create($data);

            // Auto-assign if no sales assigned explicitly
            if (empty($lead->assigned_sales_id)) {
                $this->assignmentService->autoAssign($lead);
            }

            // Calculate initial score
            $this->scoringService->recalculate($lead);

            // Log creation activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id() ?? $lead->assigned_sales_id ?? $lead->company->users()->first()?->id,
                'activity_type' => 'system',
                'activity_date' => now(),
                'result' => "Lead {$lead->name} berhasil ditambahkan ke sistem",
                'notes' => "Sumber: {$lead->source->label()}",
            ]);

            $this->auditLogService->log('lead_created', $lead, null, $lead->toArray());

            return $lead;
        });
    }

    /**
     * Update lead details.
     */
    public function update(Lead $lead, array $data): Lead
    {
        return DB::transaction(function () use ($lead, $data) {
            $before = $lead->toArray();
            $lead->update($data);

            // Recalculate score after updating profile/budget
            $this->scoringService->recalculate($lead);

            $this->auditLogService->log('lead_updated', $lead, $before, $lead->toArray());

            return $lead;
        });
    }

    /**
     * Update pipeline stage / status with business validation.
     */
    public function updateStatus(Lead $lead, LeadStatus $newStatus, ?string $lostReason = null, ?string $notes = null): Lead
    {
        if ($newStatus === LeadStatus::LOST && empty($lostReason)) {
            throw new InvalidArgumentException('Alasan gagal (lost reason) wajib diisi ketika menandai lead sebagai LOST.');
        }

        return DB::transaction(function () use ($lead, $newStatus, $lostReason, $notes) {
            $oldStatus = $lead->status;

            $lead->update([
                'status' => $newStatus,
                'lost_reason' => $newStatus === LeadStatus::LOST ? $lostReason : $lead->lost_reason,
            ]);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'activity_type' => 'status_change',
                'activity_date' => now(),
                'result' => "Tahapan pipeline berubah: {$oldStatus->label()} → {$newStatus->label()}",
                'notes' => $notes ?? ($lostReason ? "Alasan: {$lostReason}" : null),
            ]);

            $this->scoringService->recalculate($lead);

            $this->auditLogService->log('lead_status_changed', $lead, ['status' => $oldStatus->value], [
                'status' => $newStatus->value,
                'lost_reason' => $lostReason,
            ]);

            return $lead;
        });
    }

    /**
     * Query leads with multi-criteria filtering and pagination.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Lead::query()
            ->with(['assignedSales', 'interestedProject'])
            ->latest('created_at');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['temperature'])) {
            $query->where('temperature', $filters['temperature']);
        }

        if (!empty($filters['sales_id'])) {
            $query->where('assigned_sales_id', $filters['sales_id']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('interested_project_id', $filters['project_id']);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
