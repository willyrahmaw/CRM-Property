<?php

namespace App\Services\LeadAssignment;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\LeadAssignment\Contracts\LeadAssignmentStrategy;
use App\Services\LeadAssignment\Strategies\ProjectStrategy;
use App\Services\LeadAssignment\Strategies\RoundRobinStrategy;
use App\Services\LeadAssignment\Strategies\WorkloadStrategy;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class LeadAssignmentService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Resolve assignment strategy by key.
     */
    public function getStrategy(string $strategyKey = 'round_robin'): LeadAssignmentStrategy
    {
        return match ($strategyKey) {
            'round_robin' => new RoundRobinStrategy(),
            'workload' => new WorkloadStrategy(),
            'project' => new ProjectStrategy(),
            default => throw new InvalidArgumentException("Unknown lead assignment strategy: {$strategyKey}"),
        };
    }

    /**
     * Automatically assign lead using specified strategy.
     */
    public function autoAssign(Lead $lead, string $strategyKey = 'round_robin'): ?User
    {
        $strategy = $this->getStrategy($strategyKey);
        $assignedSales = $strategy->assign($lead);

        if ($assignedSales) {
            $this->applyAssignment(
                lead: $lead,
                sales: $assignedSales,
                method: "Otomatis ({$strategy->name()})"
            );
        }

        return $assignedSales;
    }

    /**
     * Manually assign lead to a specific sales agent.
     */
    public function assignManually(Lead $lead, User $sales, ?string $reason = null): void
    {
        $method = $reason ? "Manual: {$reason}" : "Manual Assignment";
        $this->applyAssignment($lead, $sales, $method);
    }

    /**
     * Apply assignment, save changes, and log activity & audit trail.
     */
    protected function applyAssignment(Lead $lead, User $sales, string $method): void
    {
        $oldSalesId = $lead->assigned_sales_id;
        $lead->update([
            'assigned_sales_id' => $sales->id,
        ]);

        // Record activity in lead timeline
        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id() ?? $sales->id,
            'activity_type' => 'system',
            'activity_date' => now(),
            'result' => "Lead dialihkan/ditugaskan kepada {$sales->name}",
            'notes' => "Metode penugasan: {$method}",
        ]);

        // Record in audit log
        $this->auditLogService->log(
            action: 'lead_reassigned',
            entity: $lead,
            before: ['assigned_sales_id' => $oldSalesId],
            after: ['assigned_sales_id' => $sales->id, 'method' => $method]
        );
    }
}
