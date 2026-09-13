<?php

namespace App\Services\LeadAssignment\Strategies;

use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadAssignment\Contracts\LeadAssignmentStrategy;

class WorkloadStrategy implements LeadAssignmentStrategy
{
    public function assign(Lead $lead, array $context = []): ?User
    {
        return User::query()
            ->where('company_id', $lead->company_id)
            ->where('is_active', true)
            ->whereIn('role', [UserRole::SALES_AGENT, UserRole::TEAM_LEADER])
            ->withCount(['assignedLeads' => function ($query) {
                $query->whereNotIn('status', [LeadStatus::WON, LeadStatus::LOST]);
            }])
            ->orderBy('assigned_leads_count', 'asc')
            ->first();
    }

    public function name(): string
    {
        return 'Workload Balance';
    }
}
